import { api } from "@/lib/axios";
import { DashboardCardStatsResponse } from "@/types/dashboard";

export async function fetchDashboardCardStats(buildingId: number | null): Promise<DashboardCardStatsResponse> {
  const { data } = await api.get<DashboardCardStatsResponse>(`/dashboard/${buildingId}/cards`);
  return data;
}
