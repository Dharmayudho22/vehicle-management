<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Booking;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->guard('web')->user();

        $query = Approval::with([
            'booking.vehicle',
            'booking.driver.user',
            'booking.requester',
            'booking.approvals.approver',
            'booking.approvalLevel1.approver',
            'booking.approvalLevel2.approver',
        ]);

        if ($user->isAdmin()) {
            // Admin lihat semua approval
            $query->latest();
        } else {
            // Approver hanya lihat miliknya
            $query->where('approver_id', $user->id)->latest();
        }

        $approvals = $query->paginate(15);

        AppLog::record('VIEW', 'Approval');

        return view('approvals.index', compact('approvals'));
    }

    public function show(Approval $approval)
    {
        /** @var \App\Models\User $user */
        $user = auth()->guard('web')->user();

        abort_unless($approval->approver_id === $user->id, 403);

        $approval->load([
            'booking.vehicle',
            'booking.driver.user',
            'booking.requester',
            'booking.approvals.approver'
        ]);

        return view('approvals.show', compact('approval'));
    }

    public function approve(Request $request, Approval $approval)
    {
        /** @var \App\Models\User $user */
        $user = auth()->guard('web')->user();

        abort_unless($approval->approver_id === $user->id, 403);
        abort_unless($approval->isPending(), 403, 'Sudah diproses.');

        // Level 2 hanya bisa approve setelah level 1 approve
        if ($approval->level === 2) {
            $level1 = $approval->booking->approvalLevel1;
            abort_unless(
                $level1 && $level1->status === 'approved',
                403,
                'Menunggu persetujuan Level 1 terlebih dahulu.'
            );
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($approval, $request) {
            $approval->update([
                'status' => 'approved',
                'notes' => $request->notes,
                'approved_at' => now(),
            ]);

            $booking = $approval->booking;

            // Cek apakah semua level sudah approved
            $allApproved = $booking->approvals()
                ->where('status', '!=', 'approved')
                ->doesntExist();

            if ($allApproved) {
                $booking->update(['status' => 'approved']);
                $booking->vehicle->update(['status' => 'in_use']);
            }

            AppLog::record('APPROVE', 'Approval', [
                'booking_id' => $booking->id,
                'level' => $approval->level,
            ]);
        });

        return redirect()->route('approvals.index')
            ->with('success', 'Pemesanan berhasil disetujui.');
    }

    public function reject(Request $request, Approval $approval)
    {
        /** @var \App\Models\User $user */
        $user = auth()->guard('web')->user();
        
        abort_unless($approval->approver_id === $user->id, 403);
        abort_unless($approval->isPending(), 403, 'Sudah diproses.');

        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($approval, $request) {
            $approval->update([
                'status' => 'rejected',
                'notes' => $request->notes,
                'approved_at' => now(),
            ]);

            $booking = $approval->booking;
            $booking->update(['status' => 'rejected']);

            // Jika level 1 reject, otomatis tutup level 2
            if ($approval->level === 1) {
                $booking->approvalLevel2?->update([
                    'status' => 'rejected',
                    'notes' => 'Otomatis ditolak karena Level 1 menolak.',
                ]);
            }

            AppLog::record('REJECT', 'Approval', [
                'booking_id' => $booking->id,
                'level' => $approval->level,
                'reason' => $request->notes,
            ]);
        });

        return redirect()->route('approvals.index')
            ->with('success', 'Pemesanan berhasil ditolak.');
    }
}

