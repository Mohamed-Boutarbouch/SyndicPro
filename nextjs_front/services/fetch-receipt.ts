import { api } from "@/lib/axios";
import type { Receipt } from "@/types/contribution";

export async function fetchReceipt(receiptId: number | null): Promise<Receipt | null> {
  if (!receiptId) return null;

  const response = await api.get<Blob>(`/receipts/${receiptId}/download`, {
    responseType: "blob",
  });

  return {
    id: receiptId,
    number: `receipt-${receiptId}.pdf`,
    blob: response.data,
  };
}

export function getReceiptUrl(receipt: Receipt) {
  return window.URL.createObjectURL(receipt.blob);
}
