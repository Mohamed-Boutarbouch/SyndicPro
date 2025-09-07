import { api } from "@/lib/axios";
import { AvailableYears } from "@/types/contribution";

export async function fetchAvailableYears(buildingId: number | null): Promise<AvailableYears[]> {
  const { data } = await api.get<AvailableYears[]>(`/contributions/${buildingId}/years`);
  return data;
}
