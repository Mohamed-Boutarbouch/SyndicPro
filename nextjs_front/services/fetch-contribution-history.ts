import { api } from "@/lib/axios";
import { PaymentHistoryResponse } from "@/types/contribution";

export async function fetchContributionHistory(buildingId: number | null, year: number): Promise<PaymentHistoryResponse[]> {
  const { data } = await api.get<PaymentHistoryResponse[]>(`/contributions/${buildingId}/history/${year}`);
  return data;
}
