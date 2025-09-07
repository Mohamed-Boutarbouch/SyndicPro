"use client"

import { Button } from "@/components/ui/button"
import { Calendar } from "lucide-react"

type Year = {
  regularContributionId: number
  year: number
}

export function YearPagination({
  years,
  selectedYear,
  onYearChange,
}: {
  years: Year[]
  selectedYear: number
  onYearChange: (year: number) => void
}) {
  const sortedYears = [...years].sort((a, b) => a.year - b.year)

  const index = sortedYears.findIndex((y) => y.year === selectedYear)

  const prevYear = () => {
    if (index > 0) {
      onYearChange(sortedYears[index - 1].year)
    }
  }

  const nextYear = () => {
    if (index < sortedYears.length - 1) {
      onYearChange(sortedYears[index + 1].year)
    }
  }

  return (
    <div className="flex items-center gap-4">
      <Button
        variant="outline"
        size="icon"
        onClick={prevYear}
        disabled={index === 0}
      >
        ←
      </Button>

      <div className="flex items-center gap-2 font-medium">
        <Calendar className="h-4 w-4" />
        {selectedYear}
      </div>

      <Button
        variant="outline"
        size="icon"
        onClick={nextYear}
        disabled={index === sortedYears.length - 1}
      >
        →
      </Button>
    </div>
  )
}
