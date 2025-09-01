"use client";

import { ContributionDataTable, ContributionScheduleResponse } from "@/components/contribution-data-table";
import { RegisterPaymentDialog } from "@/components/register-payment-dialog";
import { useSyndic } from "@/providers/syndic-provider";
import { fetchContributionSchedule } from "@/services/fetch-contribution-schedule";
import useSWR from "swr";

export default function ContributionPage() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;

  const { data, error, isLoading } = useSWR<ContributionScheduleResponse[]>(
    buildingId ? `/contributions/building/${buildingId}/schedule` : null,
    () => fetchContributionSchedule(buildingId!)
  );

  if (isLoading) return <div>Loading...</div>
  if (error) return <div>Error loading data</div>

  return (
    <div className="flex flex-col gap-4 p-8 md:gap-6 md:p-10">
      {buildingId && <RegisterPaymentDialog buildingId={buildingId} />}
      <ContributionDataTable data={data || []} />
    </div>
  );
}
