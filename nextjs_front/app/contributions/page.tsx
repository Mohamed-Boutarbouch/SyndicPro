"use client";

import { ContributionDataTable } from "@/components/contribution-data-table";
import { OverallYearlyContributionCard } from "@/components/overall-yearly-contribution-card";
import { RegisterPaymentDialog } from "@/components/register-payment-dialog";
import { useSyndic } from "@/providers/syndic-provider";
import { fetchContributionSchedule } from "@/services/fetch-contribution-schedule";
import { fetchContributionStats } from "@/services/fetch-contribution-stats"; // <-- new import
import { ContributionScheduleResponse, BuildingContributionStats } from "@/types/contribution";
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
    buildingId ? `/contributions/building/${buildingId}/schedule` : null,
    () => fetchContributionSchedule(buildingId!)
  );

  const {
    data: statsData,
    error: statsError,
    isLoading: statsLoading,
  } = useSWR<BuildingContributionStats>(
    buildingId ? `/contributions/building/${buildingId}/stats/year/${currentYear}` : null,
    () => fetchContributionStats(buildingId!, currentYear)
  );

  if (scheduleLoading || statsLoading) return <div>Loading...</div>;
  if (scheduleError || statsError) return <div>Error loading data</div>;

  console.log(statsData)
  return (
    <div className="flex flex-col gap-4 p-8 md:gap-6 md:p-10">
      {buildingId && <RegisterPaymentDialog buildingId={buildingId} currentYear={currentYear} />}

      <OverallYearlyContributionCard currentContribution={statsData} />

      <ContributionDataTable data={scheduleData || []} />
    </div>
  );
}
