import { api } from "@/lib/axios";
import { ExpensesDistributionResponse } from "@/types/dashboard";

export async function fetchExpensesDistribution(buildingId: number | null): Promise<ExpensesDistributionResponse> {
  const { data } = await api.get<ExpensesDistributionResponse>(`/dashboard/building/${buildingId}/expenses-distribution`);
  return data;
}
