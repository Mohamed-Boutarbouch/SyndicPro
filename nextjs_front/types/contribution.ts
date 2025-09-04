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
