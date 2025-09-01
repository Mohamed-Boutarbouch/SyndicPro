"use client";

import useSWR from "swr";
import { SectionCards } from "@/components/section-cards";
import { useSyndic } from "@/providers/syndic-provider";
import { DashboardCardResponse, ExpensesDistributionResponse, MonthlyIncomeExpensesResponse } from "@/types/dashboard";
import { fetchDashboardStats } from "@/services/fetchCardStats";
import { fetchMonthlyIncomeExpenses } from "@/services/fetchMonthlyIncomeExpenses";
import { ChartMonthlyIncomeExpenses } from "@/components/chart-monthly-income-expenses";
import { ChartPieDonutExpenses } from "@/components/chart-pie-expenses";
import { fetchExpensesDistribution } from "@/services/fetch-expenses-distribution";
import { TransactionDataTable } from "@/components/transaction-data-table";

export default function Page() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;

  const { data: dashboardData, error: dashboardError, isLoading: dashboardLoading } = useSWR<DashboardCardResponse>(
    buildingId ? [`/dashboard/building/${buildingId}`, buildingId] : null,
    () => fetchDashboardStats(buildingId!)
  );

  const { data: monthlyData, error: monthlyError, isLoading: monthlyLoading } = useSWR<MonthlyIncomeExpensesResponse>(
    dashboardData ? [`/dashboard/building/${buildingId}/income-expenses`, buildingId] : null,
    () => fetchMonthlyIncomeExpenses(buildingId!)
  );

  const { data: expensesData, error: expensesError, isLoading: expensesLoading } = useSWR<ExpensesDistributionResponse>(
    dashboardData ? [`/dashboard/building/${buildingId}/expenses-distribution`, buildingId] : null,
    () => fetchExpensesDistribution(buildingId!)
  );

  if (!syndic) return <p>Loading syndic...</p>;
  if (dashboardLoading) return <p>Loading dashboard...</p>;
  if (dashboardError) return <p>Error loading dashboard</p>;
  if (monthlyLoading) return <p>Loading monthly data...</p>;
  if (monthlyError) return <p>Error loading monthly data</p>;
  if (expensesLoading) return <p>Loading monthly data...</p>;
  if (expensesError) return <p>Error loading monthly data</p>;

  return (
    <div className="flex flex-1 flex-col">
      <div className="@container/main flex flex-1 flex-col gap-2">
        <div className="flex flex-col gap-4 p-8 md:gap-6 md:p-10">
          <SectionCards cardStats={dashboardData!} />
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <ChartMonthlyIncomeExpenses monthlyData={monthlyData!.monthlyIncomeExpenses} />
            <ChartPieDonutExpenses expensesData={expensesData!.expensesDistribution} />
          </div>
          <TransactionDataTable />
        </div>
      </div>
    </div>
  );
}
