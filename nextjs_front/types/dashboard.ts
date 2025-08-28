export interface ActiveUnit {
  count: number;
  type: 'apartment' | 'commercial_local' | 'other';
};

export interface DashboardResponse {
  activeUnits: ActiveUnit[];
  balancePercentChange: number | null;
  currentBalance: number;
  currentMonthIncome: number;
  incomePercentChange: number | null;
  lastMonthBalance: number;
  previousMonthIncome: number;
  totalPendingAmount: number;
  totalPendingItems: number;
};
