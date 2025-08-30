"use client"

import { Pie, PieChart, Cell } from "recharts"
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import {
  ChartContainer,
  ChartTooltip,
  ChartTooltipContent,
} from "@/components/ui/chart"
import { ExpensesDistributionResponse } from "@/types/dashboard"

const expenseDataColorMap = [
  { color: 'hsl(217, 91%, 60%)' }, // blue
  { color: 'hsl(142, 76%, 36%)' }, // green
  { color: 'hsl(38, 92%, 50%)' },  // yellow
  { color: 'hsl(0, 84%, 60%)' },   // red
  { color: 'hsl(214, 32%, 91%)' }, // gray
]

const chartConfig = {
  value: {
    label: "Value",
  },
  name: {
    label: "Category",
  },
}

interface ChartPieDonutExpensesProps {
  expensesData: ExpensesDistributionResponse["expensesDistribution"];
}

export function ChartPieDonutExpenses({ expensesData }: ChartPieDonutExpensesProps) {
  return (
    <Card className="shadow-card">
      <CardHeader>
        <CardTitle className="text-lg font-semibold">Expense Distribution</CardTitle>
      </CardHeader>
      <CardContent>
        <ChartContainer config={chartConfig} className="mx-auto aspect-square max-h-[250px]">
          <PieChart>
            <ChartTooltip
              cursor={false}
              content={<ChartTooltipContent hideLabel />}
            />
            <Pie
              data={expensesData}
              dataKey="value"
              nameKey="name"
              innerRadius={60}
              outerRadius={100}
              paddingAngle={2}
            >
              {expensesData.map((_, index) => (
                <Cell
                  key={`cell-${index}`}
                  fill={expenseDataColorMap[index % expenseDataColorMap.length].color}
                />
              ))}
            </Pie>
          </PieChart>
        </ChartContainer>

        {/* Legend */}
        <div className="mt-4 grid grid-cols-2 gap-2">
          {expensesData.map((item, index) => (
            <div key={index} className="flex items-center space-x-2">
              <div
                className="w-3 h-3 rounded-full"
                style={{ backgroundColor: expenseDataColorMap[index % expenseDataColorMap.length].color }}
              />
              <span className="text-sm text-muted-foreground">{item.name}</span>
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  )
}
