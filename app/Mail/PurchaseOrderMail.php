<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public PurchaseOrder $po;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $po)
    {
        $this->po = $po;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Purchase Order Resmi - ' . $this->po->po_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.po_sent',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.purchase_order', ['po' => $this->po])
                ->setPaper('a4', 'potrait');
        return [
            Attachment::fromData(fn () => $pdf->output(), 'Surat_PO_' . $this->po->po_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
