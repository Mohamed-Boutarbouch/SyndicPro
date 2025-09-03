export enum ContributionFrequency {
  MONTHLY = 'monthly',
  BI_MONTHLY = 'bi_monthly',
  QUARTERLY = 'quarterly',
  FOUR_MONTHLY = 'four_monthly',
  YEARLY = 'yearly'
}

export interface ContributionScheduleResponse {
  unitId: number;
  unitNumber: string;
  ownerFullName: string;
  frequency: ContributionFrequency;
  amountPerPayment: number;
  nextDueDate: string | null;
  totalPaid: number;
  paymentStatus: "paid" | "overdue";
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
  totalPayments: number;
}
