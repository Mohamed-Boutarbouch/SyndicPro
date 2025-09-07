import { api } from "@/lib/axios";
import { Transaction } from "@/types/dashboard";

export async function fetchTransactions(buildingId: number | null): Promise<Transaction[]> {
  const { data } = await api.get<Transaction[]>(`/dashboard/${buildingId}/transactions`);
  return data;
}
