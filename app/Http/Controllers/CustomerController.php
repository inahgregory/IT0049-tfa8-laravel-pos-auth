<?php
namespace App\Http\Controllers;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::orderBy('id')->get();
        return view('customers', compact('customers'));
    }
    public function create(): View
    {
        return view('customers.form', ['customer' => new Customer()]);
    }
    public function store(CustomerRequest $request): RedirectResponse
    {
        Customer::create($this->attributes($request));
        return redirect()->route('customers.index')->with('success', 'Customer account created successfully.');
    }
    public function edit(Customer $customer): View
    {
        return view('customers.form', compact('customer'));
    }
    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->attributes($request));
        return redirect()->route('customers.index')->with('success', 'Customer account updated successfully.');
    }
    public function show(Customer $customer): View
    {
        return view('customers.show', compact('customer'));
    }
    public function destroy(\Illuminate\Http\Request $request, Customer $customer): RedirectResponse
    {
        $request->validate(['confirm_delete' => ['accepted']]);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer account deleted successfully.');
    }
    private function attributes(CustomerRequest $request): array
    {
        $data = $request->validated();
        $data['phone'] = $data['phone'] ?? '';
        $data['status'] = $data['status'] ?? 'Active';
        $data['balance'] = $data['balance'] ?? 0;
        return $data;
    }
}

