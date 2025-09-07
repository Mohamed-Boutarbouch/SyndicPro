import { api } from "@/lib/axios";
import { ContributionScheduleResponse } from "@/types/contribution";

export async function fetchContributionSchedule(buildingId: number | null, year: number): Promise<ContributionScheduleResponse[]> {
  const { data } = await api.get<ContributionScheduleResponse[]>(`/contributions/${buildingId}/schedule/${year}`);
  return data;
}
