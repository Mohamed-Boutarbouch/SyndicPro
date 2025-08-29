import { IconBuildings, IconInfoTriangle, IconMoneybag, IconTrendingUp } from "@tabler/icons-react"

import {
  Card,
  CardAction,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { DashboardCardResponse } from "@/types/dashboard"
import { formatMoney } from "@/lib/formatMoney";

type SectionCardsProps = {
  cardStats: DashboardCardResponse;
};

export function SectionCards({ cardStats }: SectionCardsProps) {

  return (
    <div className="*:data-[slot=card]:from-primary/5 *:data-[slot=card]:to-card dark:*:data-[slot=card]:bg-card grid grid-cols-1 gap-4 px-4 *:data-[slot=card]:bg-gradient-to-t *:data-[slot=card]:shadow-xs lg:px-6 @xl/main:grid-cols-2 @5xl/main:grid-cols-4">
      <Card className="@container/card">
        <CardHeader>
          <CardDescription>Total Balance</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
            {formatMoney(cardStats.currentBalance)}
          </CardTitle>
          <CardAction>
            <IconMoneybag stroke={1.5} />
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
          <div className="text-muted-foreground">
            {cardStats.balancePercentChange ?? 0}% from last month
          </div>
        </CardFooter>
      </Card>
      <Card className="@container/card">
        <CardHeader>
          <CardDescription>Monthly Income</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
            {formatMoney(cardStats.currentMonthIncome)}
          </CardTitle>
          <CardAction>
            <IconTrendingUp stroke={1.5} />
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
          <div className="text-muted-foreground">
            {cardStats.incomePercentChange ?? 0}% from last month
          </div>
        </CardFooter>
      </Card>
      <Card className="@container/card">
        <CardHeader>
          <CardDescription>Pending Assessments</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
            {formatMoney(cardStats.totalPendingAmount)}
          </CardTitle>
          <CardAction>
            <IconInfoTriangle stroke={1.5} />
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
          <div className="text-destructive">{cardStats.totalPendingItems} units outstanding</div>
        </CardFooter>
      </Card>
      <Card className="@container/card">
        <CardHeader>
          <CardDescription>Active Units</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
            {cardStats.totalActiveUnits}
          </CardTitle>
          <CardAction>
            <IconBuildings stroke={1.5} />
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
          <div className="text-muted-foreground">
            <span>{cardStats.activeUnits.map(unit => unit.type === 'apartment' ? `${unit.count} residential` : '')}</span>
            <span>, </span>
            <span>{cardStats.activeUnits.map(unit => unit.type === 'commercial_local' ? `${unit.count} commercial` : '')}</span>
          </div>
        </CardFooter>
      </Card>
    </div>
  )
}
