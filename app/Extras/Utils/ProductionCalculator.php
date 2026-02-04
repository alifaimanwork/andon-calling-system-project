<?php

declare(strict_types=1);

namespace App\Extras\Utils;

use App\Models\ProductionLine;

class ProductionCalculator
{
    public static function calculateOee(ProductionLine $productionLine, \ArrayObject | array $productionRuntimeSummary, \ArrayObject | array $productionLineSummary, &$balanceTime): array
    {
        //compute standard_output, variance, availability, performance, quality, oee
        $standardOutput = self::getStandardOutput($productionLine, $productionRuntimeSummary, $balanceTime);
        $okCount = $productionLineSummary['actual_output'] - $productionLineSummary['reject_count'];
        $variance = $okCount - $standardOutput;

        $availability = self::getAvailability($productionRuntimeSummary);
        $performance = self::getPerformance($productionLineSummary, $standardOutput);
        $quality = self::getQuality($productionLineSummary);
        $oee = self::getOee($availability, $performance, $quality);

        $planVariance = $productionLineSummary['plan_quantity'] - $okCount;
        
        $result = [
            'actual_output' => $productionLineSummary['actual_output'],
            'reject_count' => $productionLineSummary['reject_count'],
            'ok_count' => $okCount,
            'pending_count' => $productionLineSummary['pending_count'],
            'plan_quantity' => $productionLineSummary['plan_quantity'],
            'variance' => $variance,

            'standard_output' => $standardOutput,
            'availability' => $availability,
            'performance' => $performance,
            'quality' => $quality,
            'oee' => $oee,
            'plan_variance' => $planVariance,
        ];
        return $result;
    }
    private static function getStandardOutput(ProductionLine $productionLine, \ArrayObject | array $productionRuntimeSummary, &$balanceTime): int|float
    {
        //check part cycle time exist

        if (!$productionLine->part_data || !$productionLine->part_data['cycle_time'] || $productionLine->part_data['cycle_time'] <= 0)
            return 0;

        //check plan runtime block exist
        $timer = $productionRuntimeSummary['runtime_summary']['runtimes']['good'] ?? null;
        if (!$timer)
            return 0;

        $cycleTime = $productionLine->part_data['cycle_time'];
        $accumulated = 0;
        /* Standard Output Calculation
        $timerBlocks = $timer['blocks'] ?? null;
        
        if (is_array($timerBlocks)) {
            foreach ($timerBlocks as $timerBlock) {
                $accumulated += floor($timerBlock / $cycleTime);
            }
        }*/
        $accumulated = floor(($timer['duration'] + $balanceTime) / $cycleTime); //simplified (not consider block timer)
        $balanceTime = ($timer['duration'] + $balanceTime) % $cycleTime;
        return $accumulated;
    }

    private static function getAvailability(\ArrayObject |array $productionRuntimeSummary): float
    {

        $totalRunningTime = $productionRuntimeSummary['runtime_summary']['runtimes']['plan']['duration'] ?? 0;
        $totalGoodRunningTime = $productionRuntimeSummary['runtime_summary']['runtimes']['good']['duration'] ?? 0;

        if ($totalRunningTime > 0)
            return $totalGoodRunningTime / $totalRunningTime;
        else
            return 0;

        return 0;
    }
    private static function getPerformance(\ArrayObject |array $productionLineSummary, float $standard_output): float
    {
        if ($standard_output <= 0)
            return 0;


        $performance = $productionLineSummary['actual_output'] / $standard_output;

        if ($performance > 1)
            $performance = 1;

        return $performance;
    }
    private static function getQuality(\ArrayObject |array $productionLineSummary): float
    {
        if ($productionLineSummary['actual_output'] <= 0)
            return 0;

        return ($productionLineSummary['actual_output'] - $productionLineSummary['reject_count']) / $productionLineSummary['actual_output'];
    }
    private static function getOee(float $availability, float $performance, float $quality): float
    {
        return $availability * $performance * $quality;
    }
}
