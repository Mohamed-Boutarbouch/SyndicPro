export enum ContributionFrequency {
  MONTHLY = 'monthly',
  BI_MONTHLY = 'bi_monthly',
  QUARTERLY = 'quarterly',
  FOUR_MONTHLY = 'four_monthly',
  YEARLY = 'yearly'
}

export interface ContributionScheduleResponse {
  amountPerPayment: number;
  frequency: ContributionFrequency;
  paymentStatus: "paid" | "overdue";
  nextDueDate: string;
  ownerName: string;
  totalPaid: number;
  unitId: number;
  unitNumber: string;
}
