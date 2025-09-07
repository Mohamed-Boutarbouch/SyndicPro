"use client";

import useSWR from "swr";
import { SectionCards } from "@/components/section-cards";
import { useSyndic } from "@/providers/syndic-provider";
import { DashboardCardStatsResponse, ExpensesDistributionResponse, MonthlyIncomeExpensesResponse, Transaction } from "@/types/dashboard";
import { fetchDashboardCardStats } from "@/services/fetch-card-stats";
import { fetchMonthlyIncomeExpenses } from "@/services/fetchMonthlyIncomeExpenses";
import { ChartMonthlyIncomeExpenses } from "@/components/chart-monthly-income-expenses";
import { ChartPieDonutExpenses } from "@/components/chart-pie-expenses";
import { fetchExpensesDistribution } from "@/services/fetch-expenses-distribution";
import { TransactionDataTable } from "@/components/transaction-data-table";
import { fetchTransactions } from "@/services/fetch-transactions";

export default function Page() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;

  const { data: dashboardCardsData, error: dashboardError, isLoading: dashboardLoading } = useSWR<DashboardCardStatsResponse>(
    buildingId ? [`/dashboard/${buildingId}/cards`, buildingId] : null,
    () => fetchDashboardCardStats(buildingId!)
  );

  const { data: monthlyData, error: monthlyError, isLoading: monthlyLoading } = useSWR<MonthlyIncomeExpensesResponse[]>(
    dashboardCardsData ? [`/dashboard/${buildingId}/income-expenses`, buildingId] : null,
    () => fetchMonthlyIncomeExpenses(buildingId!)
  );

  const { data: expensesData, error: expensesError, isLoading: expensesLoading } = useSWR<ExpensesDistributionResponse[]>(
    dashboardCardsData ? [`/dashboard/${buildingId}/expenses-distribution`, buildingId] : null,
    () => fetchExpensesDistribution(buildingId!)
  );

  const { data: transactionsData, error: transactionsError, isLoading: transactionsLoading } = useSWR<Transaction[]>(
    dashboardCardsData ? [`/dashboard/${buildingId}/transactions`, buildingId] : null,
    () => fetchTransactions(buildingId!)
  );

  if (!syndic) return <p>Loading syndic...</p>;
  if (dashboardLoading) return <p>Loading dashboard...</p>;
  if (dashboardError) return <p>Error loading dashboard</p>;
  if (monthlyLoading) return <p>Loading monthly data...</p>;
  if (monthlyError) return <p>Error loading monthly data</p>;
  if (expensesLoading) return <p>Loading monthly data...</p>;
  if (expensesError) return <p>Error loading monthly data</p>;
  if (transactionsLoading) return <p>Loading transactions data...</p>;
  if (transactionsError) return <p>Error loading transactions data</p>;

  return (
    <div className="flex flex-1 flex-col">
      <div className="@container/main flex flex-1 flex-col gap-2">
        <div className="flex flex-col gap-4 p-8 md:gap-6 md:p-10">
          <SectionCards cardStats={dashboardCardsData!} />
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <ChartMonthlyIncomeExpenses monthlyData={monthlyData || []} />
            <ChartPieDonutExpenses expensesData={expensesData || []} />
          </div>
          <TransactionDataTable transactionsData={transactionsData || []} />
        </div>
      </div>
    </div>
  );
}
