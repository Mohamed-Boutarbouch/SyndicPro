"use client";

import { ContributionDataTable } from "@/components/contribution-data-table";
import { HistoryDataTable } from "@/components/history-data-table";
import { OverallYearlyContributionCard } from "@/components/overall-yearly-contribution-card";
import { RegisterPaymentDialog } from "@/components/register-payment-dialog";
import { useSyndic } from "@/providers/syndic-provider";
import { fetchContributionHistory } from "@/services/fetch-contribution-history";
import { fetchContributionSchedule } from "@/services/fetch-contribution-schedule";
import { fetchContributionStats } from "@/services/fetch-contribution-stats";
import { fetchAvailableYears } from "@/services/fetch-available-years";
import useSWR from "swr";
import {
  ContributionScheduleResponse,
  BuildingContributionStats,
  PaymentHistoryResponse,
  AvailableYears,
} from "@/types/contribution";
import { useState, useEffect } from "react";
import { YearPagination } from "@/components/year-pagination";

export default function ContributionPage() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;

  // Fetch available years (objects with { regularContributionId, year })
  const { data: years, isLoading: yearsLoading, error: yearsError } = useSWR<
    AvailableYears[]
  >(buildingId ? `/contributions/${buildingId}/available-years` : null, () =>
    fetchAvailableYears(buildingId!)
  );

  // Local state for selected year
  const [selectedYear, setSelectedYear] = useState<number | null>(null);

  // Set default year when years are loaded
  useEffect(() => {
    if (years && years.length > 0 && !selectedYear) {
      setSelectedYear(years[0].year); // pick the first year
    }
  }, [years, selectedYear]);

  // Fetch schedule, stats, and history for selected year
  const { data: scheduleData, isLoading: scheduleLoading, error: scheduleError } =
    useSWR<ContributionScheduleResponse[]>(
      buildingId && selectedYear
        ? `/contributions/${buildingId}/schedule/${selectedYear}`
        : null,
      () => fetchContributionSchedule(buildingId!, selectedYear!)
    );

  const { data: statsData, isLoading: statsLoading, error: statsError } =
    useSWR<BuildingContributionStats>(
      buildingId && selectedYear
        ? `/contributions/${buildingId}/stats/${selectedYear}`
        : null,
      () => fetchContributionStats(buildingId!, selectedYear!)
    );

  const { data: historyData, isLoading: historyLoading, error: historyError } =
    useSWR<PaymentHistoryResponse[]>(
      buildingId && selectedYear
        ? `/contributions/${buildingId}/history/${selectedYear}`
        : null,
      () => fetchContributionHistory(buildingId!, selectedYear!)
    );

  const isLoading =
    yearsLoading || scheduleLoading || statsLoading || historyLoading;
  const isError = yearsError || scheduleError || statsError || historyError;

  if (isLoading) return <div>Loading...</div>;
  if (isError) return <div>Error loading data</div>;
  if (!selectedYear || !years || years.length === 0)
    return <div>No year available</div>;

  return (
    <div className="flex flex-col gap-6 p-8 md:p-10">
      <div className="flex items-center justify-between">
        <YearPagination
          years={years}
          selectedYear={selectedYear}
          onYearChange={setSelectedYear}
        />

        {buildingId && statsData && (
          <RegisterPaymentDialog
            buildingId={buildingId}
            regularContributionId={statsData.regularContributionId}
            currentYear={selectedYear}
          />
        )}
      </div>

      {/* Overall yearly contribution card */}
      {statsData && (
        <OverallYearlyContributionCard currentContribution={statsData} />
      )}

      {/* Contribution tables */}
      <ContributionDataTable data={scheduleData || []} />
      <HistoryDataTable data={historyData || []} />
    </div>
  );
}
