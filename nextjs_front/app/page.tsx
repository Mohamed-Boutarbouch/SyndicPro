"use client";

import useSWR from "swr";
import { SectionCards } from "@/components/section-cards";
import { useSyndic } from "@/providers/syndic-provider";
import { DashboardCardResponse } from "@/types/dashboard";
import { fetchDashboardStats } from "@/services/fetchCardStats";

export default function Page() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;

  const { data, error, isLoading } = useSWR<DashboardCardResponse>(
    buildingId ? [`/dashboard/building/${buildingId}`, buildingId] : null,
    () => fetchDashboardStats(buildingId!)
  );

  if (!syndic) return <p>Loading syndic...</p>;
  if (isLoading) return <p>Loading dashboard...</p>;
  if (error) return <p>Error loading dashboard</p>;

  return (
    <div className="flex flex-1 flex-col">
      <div className="@container/main flex flex-1 flex-col gap-2">
        <div className="flex flex-col gap-4 py-4 md:gap-6 md:py-6">
          <SectionCards cardStats={data!} />
        </div>
      </div>
    </div>
  );
}
