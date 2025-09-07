export interface ActiveUnitCount {
  count: number;
  type: 'apartment' | 'commercial_local' | 'other';
};

export interface ActiveUnit {
  breakdownByType: ActiveUnitCount[];
  totalActiveUnits: number;
};

export interface CurrentMonthCashFlow {
  month: number;
  year: number;
  totalExpenses: number;
  totalIncome: number;
  currentBalance: number;
}

export interface DashboardCardStatsResponse {
  activeApartments: number;
  activeCommercialUnits: number;
  activeUnits: ActiveUnit;
  actualBalance: number;
  currentMonthCashFlow: CurrentMonthCashFlow;
  totalExpenses: number;
  totalIncome: number;
};

export interface MonthlyIncomeExpensesResponse {
  expenses: number;
  income: number;
  month: number;
  monthFullName: string;
  monthName: string;
  net: number;
  period: string;
  year: number;

}

export interface ExpensesDistributionItem {
  name: string;
  value: number;
}

export interface ExpensesDistributionResponse {
  expensesDistribution: ExpensesDistributionItem[];
}
