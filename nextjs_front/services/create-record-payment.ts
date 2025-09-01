import { api } from "@/lib/axios";

export async function createPaymentRecord(url: string, { arg }: { arg: any }) {
  const response = await api.post(url, arg);
  return response.data;
}
