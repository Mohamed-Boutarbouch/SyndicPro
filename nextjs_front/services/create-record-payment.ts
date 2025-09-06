import { api } from "@/lib/axios";
import { CreatePaymentRecordResponse } from "@/types/contribution";

export async function createPaymentRecord(url: string, { arg }: { arg: any }) {
  const response = await api.post<CreatePaymentRecordResponse>(url, arg);
  return response.data;
}
