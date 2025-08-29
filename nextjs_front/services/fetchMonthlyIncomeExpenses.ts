import { api } from "@/lib/axios";
import { MonthlyIncomeExpensesResponse } from "@/types/dashboard";

export async function fetchMonthlyIncomeExpenses(buildingId: number | null): Promise<MonthlyIncomeExpensesResponse> {
  const { data } = await api.get<MonthlyIncomeExpensesResponse>(`/dashboard/building/${buildingId}/income-expenses`);
  return data;
}
