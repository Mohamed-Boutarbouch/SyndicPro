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

export interface MonthlyIncomeExpensesItem {
  month: string;
  income: number;
  expenses: number;
}

export interface MonthlyIncomeExpensesResponse {
  monthlyIncomeExpenses: MonthlyIncomeExpensesItem[];
}

export interface ExpensesDistributionItem {
  name: string;
  value: number;
}

export interface ExpensesDistributionResponse {
  expensesDistribution: ExpensesDistributionItem[];
}
