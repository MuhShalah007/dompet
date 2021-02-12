<?php

namespace Tests\Feature\Loans;

use App\Loan;
use App\Partner;
use App\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTransactionEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_add_transaction_to_a_loan()
    {
        $date = today()->format('Y-m-d');
        $user = $this->loginAsUser();
        $partner = factory(Partner::class)->create(['creator_id' => $user->id]);
        $loan = factory(Loan::class)->create([
            'partner_id' => $partner->id,
            'creator_id' => $user->id,
            'type_id'    => Loan::TYPE_DEBT,
        ]);
        $transaction = factory(Transaction::class)->create([
            'in_out'     => 1, // 0:spending, 1:income
            'loan_id'    => $loan->id,
            'amount'     => $loan->amount,
            'partner_id' => $partner->id,
            'creator_id' => $user->id,
        ]);

        $this->visitRoute('loans.show', $loan);
        $this->click('add_transaction-'.$loan->id);
        $this->visitRoute('loans.transactions.create', $loan);

        $this->submitForm(__('loan.add_transaction'), [
            'in_out'      => 0, // 0:spending, 1:income
            'amount'      => 99.99,
            'date'        => $date,
            'description' => 'Receivable transaction',
        ]);

        $this->seeRouteIs('loans.show', $loan);

        $this->seeInDatabase('transactions', [
            'loan_id'     => $loan->id,
            'date'        => $date,
            'amount'      => 99.99,
            'partner_id'  => $partner->id,
            'in_out'      => 0, // 0:spending, 1:income
            'description' => 'Receivable transaction',
        ]);
    }
}
