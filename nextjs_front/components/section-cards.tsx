import { IconBuildings, IconInfoTriangle, IconMoneybag, IconTrendingUp } from "@tabler/icons-react"

import {
  Card,
  CardAction,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { DashboardCardStatsResponse } from "@/types/dashboard"
import { formatMoney } from "@/lib/formatMoney";

type SectionCardsProps = {
  cardStats: DashboardCardStatsResponse;
};

export function SectionCards({ cardStats }: SectionCardsProps) {

  return (
    <div className="*:data-[slot=card]:from-primary/5 *:data-[slot=card]:to-card dark:*:data-[slot=card]:bg-card grid grid-cols-1 gap-4 *:data-[slot=card]:bg-gradient-to-t *:data-[slot=card]:shadow-xs @xl/main:grid-cols-2 @5xl/main:grid-cols-3">
      <Card className="@container/card">
        <CardHeader>
          <CardDescription>Total Balance</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
            {formatMoney(cardStats.actualBalance)}
          </CardTitle>
          <CardAction>
            <IconMoneybag stroke={1.5} />
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
          <div className="text-muted-foreground">
            <span className="text-green-400">Income: +{formatMoney(cardStats.totalIncome)}</span>
            <br />
            <span className="text-red-400">Expenses: -{formatMoney(cardStats.totalExpenses)}</span>
          </div>
        </CardFooter>
      </Card>
      <Card className="@container/card">
        <CardHeader>
          <CardDescription>Monthly Balance</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
            {formatMoney(cardStats.currentMonthCashFlow.currentBalance)}
          </CardTitle>
          <CardAction>
            <IconTrendingUp stroke={1.5} />
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
          <div className="text-muted-foreground">
            <span className="text-green-400">Income: +{formatMoney(cardStats.currentMonthCashFlow.totalIncome)}</span>
            <br />
            <span className="text-red-400">Expenses: -{formatMoney(cardStats.currentMonthCashFlow.totalExpenses)}</span>
          </div>
        </CardFooter>
      </Card>
      <Card className="@container/card">
        <CardHeader>
          <CardDescription>Active Units</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
            {cardStats.activeUnits.totalActiveUnits}
          </CardTitle>
          <CardAction>
            <IconBuildings stroke={1.5} />
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
          <div className="text-muted-foreground">
            <span>{`${cardStats.activeApartments} residential`}</span>
            <span>, </span>
            <span>{`${cardStats.activeCommercialUnits} commercial`}</span>
          </div>
        </CardFooter>
      </Card>
    </div>
  )
}
