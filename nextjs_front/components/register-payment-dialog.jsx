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

export function RegisterPaymentDialog() {
  const syndic = useSyndic();
  const buildingId = syndic?.building?.id;

  return (
    <Dialog>
      <DialogTrigger asChild className="w-24">
        <Button variant="outline">Record Payment</Button>
      </DialogTrigger>

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
