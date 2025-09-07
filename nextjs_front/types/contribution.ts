export enum ContributionFrequency {
  MONTHLY = 'monthly',
  BI_MONTHLY = 'bi_monthly',
  QUARTERLY = 'quarterly',
  FOUR_MONTHLY = 'four_monthly',
  YEARLY = 'yearly'
}

export enum PaymentStatus {
  OVERDUE = 'overdue',
  PENDING = 'pending',
  PAID = 'paid'
}

export enum PaymentMethod {
  CASH = "cash",
  BANK_TRANSFER = "bank_transfer",
  CHECK = "check",
  CREDIT_CARD = "credit_card",
  OTHER = "other",
}

export interface ContributionScheduleResponse {
  actualPaidAmountPerUnit: number;
  amountPerPayment: number;
  buildingId: number;
  frequency: ContributionFrequency;
  nextDueDate: string;
  ownerFullName: string;
  ownerId: number;
  paymentStatus: PaymentStatus;
  regularContributionId: number;
  scheduleId: number;
  unitFloor: number;
  unitId: number;
  unitNumber: string;
}
export interface BuildingContributionStats {
  buildingId: number;
  buildingName: string;
  paymentYear: number;
  amountPerUnit: number;
  periodStartDate: string;
  periodEndDate: string;
  regularContributionId: number;
  totalAnnualAmount: number;
  totalPaidAmount: number;
  totalPaymentCount: number;
}

export interface CreatePaymentRecordResponse {
  success: boolean;
  message: string;
  paymentId: number;
  receipt: {
    id: number;
    filePath: string;
  };
}

export interface Receipt {
  id: number;
  number: string;
  blob: Blob;
}

export interface PaymentHistoryResponse {
  buildingId: number;
  ledgerEntryId: number;
  ownerId: number;
  ownerFirstName: string;
  ownerLastName: string;
  ownerFullName: string;
  unitId: number;
  unitNumber: string;
  regularContributionId: number;

  paidAmount: number;
  paymentDate: string;
  paymentMethod: PaymentMethod;

  receiptId: number;
  receiptFilePath: string;
  referenceNumber: string;
}
