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
import { useSyndic } from "@/providers/syndic-provider"
import { Plus } from "lucide-react";

export function RegisterPaymentDialog() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;

  return (
    <Dialog>
      <div className="flex justify-end">
        <DialogTrigger asChild>
          <Button size="lg" className="text-black bg-primary hover:cursor-pointer w-40">
            <Plus className="h-4 w-4 mr-2" />
            Record Payment
          </Button>
        </DialogTrigger>
      </div>

      <DialogContent className="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>Record Contribution Payment</DialogTitle>
          <DialogDescription>
            Fill out the form below to record a new contribution payment.
          </DialogDescription>
        </DialogHeader>

        <PaymentRecordForm buildingId={buildingId} />

        <DialogFooter>
          <DialogClose asChild>
            <Button variant="outline">Cancel</Button>
          </DialogClose>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
