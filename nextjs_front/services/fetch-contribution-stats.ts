import { api } from "@/lib/axios";
import { BuildingContributionStats } from "@/types/contribution";

export async function fetchContributionStats(buildingId: number | null, year: number): Promise<BuildingContributionStats> {
  const { data } = await api.get<BuildingContributionStats>(`/contributions/${buildingId}/stats/${year}`);
  return data;
}
