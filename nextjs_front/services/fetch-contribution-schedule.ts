import { api } from "@/lib/axios";
import { ContributionScheduleResponse } from "@/types/contribution";

export async function fetchContributionSchedule(buildingId: number | null): Promise<ContributionScheduleResponse[]> {
  const { data } = await api.get<ContributionScheduleResponse[]>(`/contributions/building/${buildingId}/schedule`);
  return data;
}
