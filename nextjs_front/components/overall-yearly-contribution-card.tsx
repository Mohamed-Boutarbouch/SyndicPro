"use client"

import { BuildingContributionStats } from "@/types/contribution";
import { Badge } from "./ui/badge";
import { Card } from "./ui/card";
import { Separator } from "./ui/separator";
import { Progress } from "./ui/progress";
import { ArrowBigRight, Calendar } from "lucide-react";
import { formatMoney } from "@/lib/formatMoney";

interface Props {
  currentContribution: BuildingContributionStats | undefined;
}

export function OverallYearlyContributionCard({ currentContribution }: Props) {
  if (!currentContribution) return null;

  const collectionRate = Math.round(
    (currentContribution.totalPaidAmount / currentContribution.totalAnnualAmount) * 100
  );

  return (
    <Card className="p-6">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-2xl font-semibold text-foreground">Current Contribution Setup</h3>
        <Badge variant="outline" className="bg-success text-success-foreground">
          Active
        </Badge>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="space-y-2">
          <p className="text-lg font-medium text-muted-foreground">Period</p>
          <p className="text-xl font-semibold text-foreground">
            {currentContribution.paymentYear}
          </p>
          <p className="text-lg text-muted-foreground">

            <div className="flex items-center gap-2">
              <Calendar className="h-4 w-4 text-muted-foreground" />
              <span className="text-foreground">
                {currentContribution.periodStartDate}
              </span>
              <ArrowBigRight className="h-4 w-4 text-muted-foreground" />
              <Calendar className="h-4 w-4 text-muted-foreground" />
              <span className="text-foreground">
                {currentContribution.periodAnnualAmount}
              </span>
            </div>
          </p>
        </div>

        <Separator className="my-2 block md:hidden" />

        <div className="space-y-2">
          <p className="text-lg font-medium text-muted-foreground">Total Budget</p>
          <p className="text-xl font-semibold text-foreground">
            {formatMoney(currentContribution.totalAnnualAmount)}
          </p>
          <p className="text-lg text-muted-foreground">
            {formatMoney(currentContribution.totalAnnualAmount / 10)} per unit
          </p>
        </div>

        <Separator className="my-2 block md:hidden" />

        <div className="space-y-2">
          <p className="text-lg font-medium text-muted-foreground">Collection Progress</p>
          <Progress value={collectionRate} className="h-3 rounded-full" />

          <div className="flex justify-between text-lg text-muted-foreground mt-2">
            <span>{collectionRate}%</span>
            <span>{formatMoney(currentContribution.totalPaidAmount)} collected</span>
          </div>
        </div>
      </div>
    </Card>
  );
}
