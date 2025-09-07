"use client";

import { ContributionDataTable } from "@/components/contribution-data-table";
import { HistoryDataTable } from "@/components/history-data-table";
import { OverallYearlyContributionCard } from "@/components/overall-yearly-contribution-card";
import { RegisterPaymentDialog } from "@/components/register-payment-dialog";
import { useSyndic } from "@/providers/syndic-provider";
import { fetchContributionHistory } from "@/services/fetch-contribution-history";
import { fetchContributionSchedule } from "@/services/fetch-contribution-schedule";
import { fetchContributionStats } from "@/services/fetch-contribution-stats";
import { ContributionScheduleResponse, BuildingContributionStats, PaymentHistoryResponse } from "@/types/contribution";
import useSWR from "swr";

export default function ContributionPage() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;
  const currentYear = new Date().getFullYear();

  const {
    data: scheduleData,
    error: scheduleError,
    isLoading: scheduleLoading,
  } = useSWR<ContributionScheduleResponse[]>(
    buildingId ? `/contributions/${buildingId}/schedule/${currentYear}` : null,
    () => fetchContributionSchedule(buildingId!, currentYear)
  );

  const {
    data: statsData,
    error: statsError,
    isLoading: statsLoading,
  } = useSWR<BuildingContributionStats>(
    buildingId ? `/contributions/${buildingId}/stats/${currentYear}` : null,
    () => fetchContributionStats(buildingId!, currentYear)
  );

  const {
    data: historyData,
    error: historyError,
    isLoading: historyLoading,
  } = useSWR<PaymentHistoryResponse[]>(
    buildingId ? `/contributions/${buildingId}/history/${currentYear}` : null,
    () => fetchContributionHistory(buildingId!, currentYear)
  );

  if (scheduleLoading || statsLoading || historyLoading) return <div>Loading...</div>;
  if (scheduleError || statsError || historyError) return <div>Error loading data</div>;

  return (
    <div className="flex flex-col gap-4 p-8 md:gap-6 md:p-10">
      {buildingId && <RegisterPaymentDialog
        buildingId={buildingId}
        regularContributionId={statsData?.regularContributionId!}
        currentYear={currentYear}
      />}

      <OverallYearlyContributionCard currentContribution={statsData} />

      <ContributionDataTable data={scheduleData || []} />
      <HistoryDataTable data={historyData || []} />
    </div>
  );
}
