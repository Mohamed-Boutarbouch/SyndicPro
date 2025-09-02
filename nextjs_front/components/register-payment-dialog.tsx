import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog"
import { PaymentRecordForm } from "./forms/register-payment-form"
import { Plus } from "lucide-react";
import { useState } from "react";

export function RegisterPaymentDialog({ buildingId, currentYear }: { buildingId: number; currentYear: number }) {
  const [open, setOpen] = useState(false)

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <div className="flex justify-end">
        <DialogTrigger asChild>
          <Button size="lg" className="text-black bg-primary hover:cursor-pointer w-40">
            <Plus className="h-4 w-4 mr-2" />
            Record Payment
          </Button>
        </DialogTrigger>
      </div>

      <DialogContent className="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Record Contribution Payment</DialogTitle>
          <DialogDescription>
            Fill out the form below to record a new contribution payment.
          </DialogDescription>
        </DialogHeader>

        <PaymentRecordForm
          buildingId={buildingId}
          currentYear={currentYear}
          onSuccessAction={() => setOpen(false)}
        />

        <DialogFooter>
          <DialogClose asChild>
            <Button variant="outline">Cancel</Button>
          </DialogClose>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
