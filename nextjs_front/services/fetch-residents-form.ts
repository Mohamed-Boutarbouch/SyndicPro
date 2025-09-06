import { api } from "@/lib/axios";
import { ResidentsFormResponse } from "@/types/building";

export async function fetchResidentsForm(buildingId: number | null): Promise<ResidentsFormResponse[]> {
  const { data } = await api.get<ResidentsFormResponse[]>(`/buildings/${buildingId}/residents`);
  return data;
}
