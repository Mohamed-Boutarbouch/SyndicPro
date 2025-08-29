import { api } from "@/lib/axios";
import { DashboardCardResponse } from "@/types/dashboard";

export async function fetchDashboardStats(buildingId: number | null): Promise<DashboardCardResponse> {
  const { data } = await api.get<DashboardCardResponse>(`/dashboard/building/${buildingId}`);
  return data;
}
