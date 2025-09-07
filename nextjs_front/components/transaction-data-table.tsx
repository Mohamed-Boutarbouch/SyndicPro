"use client"

import { format, parseISO } from "date-fns"
import * as React from "react"
import {
  ColumnDef,
  ColumnFiltersState,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  SortingState,
  useReactTable,
  VisibilityState,
} from "@tanstack/react-table"
import { Calendar, ChevronDown } from "lucide-react"
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { formatMoney } from "@/lib/formatMoney"
import { Badge } from "@/components/ui/badge"
import { PaymentMethod } from "@/types/contribution"
import { cn } from "@/lib/utils"
import { Transaction, TransactionType } from "@/types/dashboard"

interface TransactionDataTableProps {
  transactionsData: Transaction[]
}

export const columns: ColumnDef<Transaction>[] = [
  {
    id: "select",
    header: ({ table }) => (
      <Checkbox
        checked={
          table.getIsAllPageRowsSelected() ||
          (table.getIsSomePageRowsSelected() && "indeterminate")
        }
        onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
        aria-label="Select all"
      />
    ),
    cell: ({ row }) => (
      <Checkbox
        checked={row.getIsSelected()}
        onCheckedChange={(value) => row.toggleSelected(!!value)}
        aria-label="Select row"
      />
    ),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: "createdAt",
    header: "Payment Date",
    cell: ({ row }) => {
      const value = row.getValue("createdAt") as string | null
      const formattedDate = value ? format(parseISO(value), "dd-MM-yyyy") : "—"

      return (
        <div className="flex items-center gap-2">
          <Calendar className="h-4 w-4 text-muted-foreground" />
          <span className="text-foreground">
            {formattedDate}
          </span>
        </div>
      )
    },
  },
  {
    id: "unitNumber",
    accessorFn: (row) => row.unit?.number ?? null,
    header: "Unit",
    cell: ({ getValue }) => {
      const value = getValue<string | null>()
      return value && value.trim() !== "" ? value : "—"
    },
  },
  {
    accessorKey: "description",
    header: "Description",
  },
  {
    accessorKey: "amount",
    header: "Amount",
    cell: ({ row }) => {
      const amount = row.getValue<number>("amount")
      const type = row.getValue<TransactionType>("type")

      return (
        <span
          className={cn(
            type === TransactionType.EXPENSE && "text-red-500 font-medium",
            type === TransactionType.INCOME && "text-green-500 font-medium"
          )}
        >
          {formatMoney(amount)}
        </span>
      )
    },
  },
  {
    accessorKey: "type",
    header: "Tranasaction Type",
    cell: ({ row }) => {
      const method = row.getValue("type") as TransactionType

      return (
        <Badge
          variant="outline"
          className={cn(
            "capitalize",
            method === TransactionType.INCOME &&
            "bg-green-100 text-green-800 border-green-200",
            method === TransactionType.EXPENSE &&
            "bg-red-100 text-red-800 border-red-200",
          )}
        >
          {method}
        </Badge>
      )
    }
  },
  {
    accessorKey: "paymentMethod",
    header: "Payment Method",
    cell: ({ row }) => {
      const method = row.getValue("paymentMethod") as PaymentMethod

      return (
        <Badge
          variant="outline"
          className={cn(
            "capitalize",
            method === PaymentMethod.CASH &&
            "bg-green-100 text-green-800 border-green-200",
            method === PaymentMethod.BANK_TRANSFER &&
            "bg-blue-100 text-blue-800 border-blue-200",
            method === PaymentMethod.CHECK &&
            "bg-purple-100 text-purple-800 border-purple-200",
            method === PaymentMethod.CREDIT_CARD &&
            "bg-pink-100 text-pink-800 border-pink-200",
            method === PaymentMethod.OTHER &&
            "bg-gray-100 text-gray-800 border-gray-200"
          )}
        >
          {method.replace("_", " ")}
        </Badge>
      )
    }
  },
  {
    accessorKey: "referenceNumber",
    header: "Reference",
    cell: ({ row }) => {
      const ref = row.getValue("referenceNumber") as string | null

      return ref && ref.trim() !== "" ? ref : "—"
    }
  }
]

export function TransactionDataTable({ transactionsData }: TransactionDataTableProps) {
  const [sorting, setSorting] = React.useState<SortingState>([])
  const [columnFilters, setColumnFilters] = React.useState<ColumnFiltersState>([])
  const [columnVisibility, setColumnVisibility] = React.useState<VisibilityState>({})
  const [rowSelection, setRowSelection] = React.useState({})

  const table = useReactTable({
    data: transactionsData,
    columns,
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    state: {
      sorting,
      columnFilters,
      columnVisibility,
      rowSelection,
    },
  })

  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-2xl">Recent Transactions</CardTitle>
      </CardHeader>
      <CardContent>
        <div className="w-full">
          <div className="flex items-center py-4">
            {/* <Input */}
            {/*   placeholder="Filter owners..." */}
            {/*   value={(table.getColumn("ownerFullName")?.getFilterValue() as string) ?? ""} */}
            {/*   onChange={(event) => */}
            {/*     table.getColumn("ownerFullName")?.setFilterValue(event.target.value) */}
            {/*   } */}
            {/*   className="max-w-sm" */}
            {/* /> */}
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="outline" className="ml-auto">
                  Columns <ChevronDown />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                {table.getAllColumns()
                  .filter((column) => column.getCanHide())
                  .map((column) => (
                    <DropdownMenuCheckboxItem
                      key={column.id}
                      className="capitalize"
                      checked={column.getIsVisible()}
                      onCheckedChange={(value) => column.toggleVisibility(!!value)}
                    >
                      {column.id}
                    </DropdownMenuCheckboxItem>
                  ))}
              </DropdownMenuContent>
            </DropdownMenu>
          </div>

          <div className="overflow-hidden rounded-md border">
            <Table>
              <TableHeader>
                {table.getHeaderGroups().map((headerGroup) => (
                  <TableRow key={headerGroup.id}>
                    {headerGroup.headers.map((header) => (
                      <TableHead key={header.id}>
                        {header.isPlaceholder
                          ? null
                          : flexRender(header.column.columnDef.header, header.getContext())}
                      </TableHead>
                    ))}
                  </TableRow>
                ))}
              </TableHeader>
              <TableBody>
                {table.getRowModel().rows.length ? (
                  table.getRowModel().rows.map((row) => (
                    <TableRow key={row.id} data-state={row.getIsSelected() && "selected"}>
                      {row.getVisibleCells().map((cell) => (
                        <TableCell key={cell.id}>
                          {flexRender(cell.column.columnDef.cell, cell.getContext())}
                        </TableCell>
                      ))}
                    </TableRow>
                  ))
                ) : (
                  <TableRow>
                    <TableCell colSpan={columns.length} className="h-24 text-center">
                      No results.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </div>

          <div className="flex items-center justify-end space-x-2 py-4">
            <div className="text-muted-foreground flex-1 text-sm">
              {table.getFilteredSelectedRowModel().rows.length} of {table.getFilteredRowModel().rows.length} row(s) selected.
            </div>
            <div className="space-x-2">
              <Button
                variant="outline"
                size="sm"
                onClick={() => table.previousPage()}
                disabled={!table.getCanPreviousPage()}
              >
                Previous
              </Button>
              <Button
                variant="outline"
                size="sm"
                onClick={() => table.nextPage()}
                disabled={!table.getCanNextPage()}
              >
                Next
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  )
}
