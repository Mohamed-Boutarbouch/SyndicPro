export interface ActiveUnit {
  count: number;
  type: 'apartment' | 'commercial_local' | 'other';
};

export interface DashboardCardResponse {
  activeUnits: ActiveUnit[];
  balancePercentChange: number | null;
  currentBalance: number;
  currentMonthIncome: number;
  incomePercentChange: number | null;
  lastMonthBalance: number;
  previousMonthIncome: number;
  totalPendingAmount: number;
  totalPendingItems: number;
  totalActiveUnits: number;
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

