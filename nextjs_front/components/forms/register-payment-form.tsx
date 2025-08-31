"use client"

import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Textarea } from "@/components/ui/textarea"
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"
import { cn } from "@/lib/utils"
import { CalendarIcon } from "lucide-react"
import { Calendar } from "@/components/ui/calendar"
import { format } from "date-fns"
import useSWR from "swr"
import { api } from "@/lib/axios"
import { ResidentsFormResponse } from "@/types/building"
import { useEffect } from "react"

export const recordPaymentSchema = z.object({
  unitId: z.string().min(1, "Unit is required."),

  amount: z.number().positive("Amount must be greater than zero."),

  paymentDate: z
    .instanceof(Date, { message: "Invalid date" })
    .refine((date) => !isNaN(date.getTime()), { message: "Invalid date" }),

  paymentMethod: z.enum([
    "bank_transfer",
    "check",
    "cash",
    "credit_card",
    "other",
  ]),

  reference: z.string().optional(),

  notes: z
    .string()
    .max(500, "Notes must be 500 characters or less.")
    .optional(),
})

export type RecordPaymentSchema = z.infer<typeof recordPaymentSchema>

const fetcher = (url: string): Promise<ResidentsFormResponse[]> => api.get(url).then((res) => res.data)

export function PaymentRecordForm({ buildingId }: { buildingId: number }) {
  const { data: residents, error, isLoading } = useSWR<ResidentsFormResponse[]>(
    buildingId ? `/buildings/${buildingId}/residents` : null,
    fetcher
  )

  const form = useForm<z.infer<typeof recordPaymentSchema>>({
    resolver: zodResolver(recordPaymentSchema),
    defaultValues: {
      unitId: "",
      amount: 0.00,
      paymentDate: new Date(),
      paymentMethod: "cash",
      reference: "",
      notes: "",
    },
  })

  const unitId = form.watch("unitId")
  const selectedResident = residents?.find(r => r.unitId.toString() === unitId)

  useEffect(() => {
    if (selectedResident) {
      form.setValue("amount", Number(selectedResident.expectedPayment))
    }
  }, [selectedResident])

  function onSubmit(values: RecordPaymentSchema) {
    const unitId = values.unitId
    const selectedResident = residents?.find(r => r.unitId.toString() === unitId)

    // If notes is empty, auto-construct it
    if (selectedResident && !values.notes) {
      const dateStr = format(values.paymentDate, "PPP")
      const residentName = `${selectedResident.firstName} ${selectedResident.lastName}`
      values.notes = `Payment of ${values.amount.toFixed(2)} MAD from ${residentName} via ${values.paymentMethod.replace('_', ' ')} on ${dateStr}${values.reference ? ` (Ref: ${values.reference})` : ''}.`
    }

    console.log(values)
  }

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
        <FormField
          control={form.control}
          name="unitId"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Schedule / Resident</FormLabel>
              <Select onValueChange={field.onChange} value={field.value}>
                <FormControl>
                  <SelectTrigger className="w-full">
                    <SelectValue
                      placeholder={
                        isLoading
                          ? "Loading..."
                          : error
                            ? "Failed to load"
                            : "Select resident"
                      }
                    />
                  </SelectTrigger>
                </FormControl>
                <SelectContent className="w-full">
                  {residents?.map((resident) => (
                    <SelectItem
                      key={resident.unitId}
                      value={resident.unitId.toString()}
                    >
                      {resident.number} | {resident.firstName} {resident.lastName}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <FormMessage />
            </FormItem>
          )}
        />

        <div className="flex gap-4">
          <FormField
            control={form.control}
            name="amount"
            render={({ field }) => (
              <FormItem className="flex-1">
                <FormLabel>Amount</FormLabel>
                <FormControl>
                  <Input
                    type="number"
                    step="0.01"
                    min={0}
                    placeholder="Enter amount"
                    {...field}
                    onChange={(e) =>
                      field.onChange(
                        parseFloat(parseFloat(e.target.value).toFixed(2))
                      )
                    }
                  />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="paymentDate"
            render={({ field }) => (
              <FormItem className="flex-1 flex flex-col">
                <FormLabel>Payment Date</FormLabel>
                <Popover>
                  <PopoverTrigger asChild>
                    <FormControl>
                      <Button
                        variant={"outline"}
                        className={cn(
                          "w-full text-left font-normal",
                          !field.value && "text-muted-foreground"
                        )}
                      >
                        {field.value ? (
                          format(field.value, "PPP")
                        ) : (
                          <span>Pick a date</span>
                        )}
                        <CalendarIcon className="ml-auto h-4 w-4 opacity-50" />
                      </Button>
                    </FormControl>
                  </PopoverTrigger>
                  <PopoverContent className="w-auto p-0" align="start">
                    <Calendar
                      mode="single"
                      selected={field.value}
                      onSelect={field.onChange}
                      disabled={(date) =>
                        date > new Date() || date < new Date("1900-01-01")
                      }
                      captionLayout="dropdown"
                    />
                  </PopoverContent>
                </Popover>
                <FormMessage />
              </FormItem>
            )}
          />
        </div>

        <div className="flex gap-4">
          <FormField
            control={form.control}
            name="paymentMethod"
            render={({ field }) => (
              <FormItem className="flex-1 flex flex-col">
                <FormLabel>Payment Method</FormLabel>
                <Select onValueChange={field.onChange} defaultValue={field.value}>
                  <FormControl>
                    <SelectTrigger className="w-full">
                      <SelectValue placeholder="Select method" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent className="w-full">
                    <SelectItem value="cash">Cash</SelectItem>
                    <SelectItem value="bank_transfer">Bank Transfer</SelectItem>
                    <SelectItem value="check">Check</SelectItem>
                    <SelectItem value="credit_card">Credit Card</SelectItem>
                    <SelectItem value="other">Other</SelectItem>
                  </SelectContent>
                </Select>
                <FormMessage />
              </FormItem>
            )}
          />

          <FormField
            control={form.control}
            name="reference"
            render={({ field }) => (
              <FormItem className="flex-1">
                <FormLabel>Reference</FormLabel>
                <FormControl>
                  <Input placeholder="Optional reference" {...field} />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
        </div>

        <FormField
          control={form.control}
          name="notes"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Notes</FormLabel>
              <FormControl>
                <Textarea placeholder="Optional notes" {...field} />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />

        <Button type="submit" className="text-black bg-primary hover:cursor-pointer">Record Payment</Button>
      </form>
    </Form>
  )
}
