<?php

namespace App\Services;

use App\Models\TicketHead;
use App\Models\User;
use App\Models\MasterPosition;
use App\Models\TicketApproval;
use App\Models\TicketApprovalRule;

class ApprovalService
{
    public static function getNextApprover(TicketHead $ticket, float $currentValue = null): array
    {
        $requestor = $ticket->user;
        if (!$requestor || !$requestor->position) {
            return ['user' => null, 'value' => null, 'position_id' => null, 'division_id' => null];
        }

        $reqPos = $requestor->position;
        $reqLevel = (float) $reqPos->level;
        $reqDivId = $reqPos->division_id;
        $reqDeptId = $reqPos->department_id;

        $currentValue = (float) ($currentValue ?? $ticket->current_approval_value ?? $reqLevel);

        $nextInDivision = User::join('positions as p', 'users.position_id', '=', 'p.id')
            ->where('users.division_id', $reqDivId)
            ->whereRaw('CAST(p.level AS DECIMAL(6,2)) > ?', [$currentValue])
            ->orderByRaw('CAST(p.level AS DECIMAL(6,2)) ASC')
            ->select('users.*', 'p.level as level')
            ->first();

        if ($nextInDivision) {
            return [
                'user' => $nextInDivision,
                'value' => (float) $nextInDivision->level,
                'position_id' => $nextInDivision->position_id,
                'division_id' => $nextInDivision->division_id,
            ];
        }

        $nextRules = TicketApprovalRule::where('menu_id', $ticket->menu_id)
            ->where('sub_menu_id', $ticket->sub_menu_id)
            ->whereRaw('CAST(level AS DECIMAL(6,2)) = FLOOR(?)', [$currentValue])
            ->whereRaw('CAST(sequence AS DECIMAL(6,2)) > ?', [$currentValue])
            ->orderByRaw('CAST(sequence AS DECIMAL(6,2)) ASC')
            ->get(); // JANGAN pakai first()

        foreach ($nextRules as $rule) {

            if (!is_null($rule->division_id) && $rule->division_id == $reqDivId) {
                continue;
            }

            $candidate = User::where('position_id', $rule->position_id)
                ->when($rule->division_id, fn($q) => $q->where('division_id', $rule->division_id))
                ->first();

            if ($candidate) {
                return [
                    'user' => $candidate,
                    'value' => (float) $rule->sequence,
                    'position_id' => $rule->position_id,
                    'division_id' => $rule->division_id,
                ];
            }
        }


        $nextInDept = User::join('positions as p', 'users.position_id', '=', 'p.id')
            ->where('users.department_id', $reqDeptId)
            ->whereRaw('CAST(p.level AS DECIMAL(6,2)) > ?', [$currentValue])
            ->orderByRaw('CAST(p.level AS DECIMAL(6,2)) ASC')
            ->select('users.*', 'p.level as level')
            ->first();

        if ($nextInDept) {
            return [
                'user' => $nextInDept,
                'value' => (float) $nextInDept->level,
                'position_id' => $nextInDept->position_id,
                'division_id' => $nextInDept->division_id,
            ];
        }

        $jmFinance = User::whereHas('position', fn($q) => $q->whereIn('level', [9, 99]))->first();
        if ($jmFinance) {
            return [
                'user' => $jmFinance,
                'value' => (float) $jmFinance->position->level,
                'position_id' => $jmFinance->position_id,
                'division_id' => $jmFinance->division_id,
            ];
        }

        $finalRule = TicketApprovalRule::where('menu_id', $ticket->menu_id)
            ->where('sub_menu_id', $ticket->sub_menu_id)
            ->where('is_final', 1)
            ->first();

        if ($finalRule) {
            $candidate = User::where('position_id', $finalRule->position_id)->first();
            if ($candidate) {
                return [
                    'user' => $candidate,
                    'value' => (float) ($finalRule->sequence ?? $finalRule->level),
                    'position_id' => $finalRule->position_id,
                    'division_id' => $finalRule->division_id,
                ];
            }
        }

        return ['user' => null, 'value' => null, 'position_id' => null, 'division_id' => null];
    }
}
