<?php

namespace App\Http\Controllers\Web\Admin;

use App\Extras\Datasets\CompanyDataset;
use App\Extras\Utils\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    //TODO: Resource Guard
    public function index(Request $request)
    {
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];
        return view('pages.web.admin.company.index', $viewData);
    }

    public function create(Request $request)
    {
        //Show create new company page

        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];
        return view('pages.web.admin.company.create', $viewData);
    }

    public function store(Request $request)
    {
        //Store new company
        //TODO: Input Validation

        //temporary validation
        $request->validate([
            'name' => ['required', 'string', 'max:64', 'unique:companies,name'],
            'code' => ['required', 'string', 'max:16', 'unique:companies,code'],
        ]);

        $newCompany = new Company($request->only(['name', 'code']));
        $newCompany->save();

        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];

        ToastHelper::addToast('New ' . $newCompany->name . ' added.', 'Create New Company');
        return view('pages.web.admin.company.index', $viewData);
    }
    public function edit(Request $request, Company $company)
    {
        //Edit company details
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS',
            'company' => $company
        ];
        return view('pages.web.admin.company.edit', $viewData);
    }
    public function show(Request $request, Company $company)
    {
        //Show company details
        if ($request->wantsJson())
            return $company;

        abort(404); //No view page
    }

    public function update(Request $request, Company $company)
    {
        //Update company

        //TODO: Input Validation

        //temporary validation
        $request->validate([
            'name' => ['required', 'string', 'max:64', 'unique:companies,name,' . $company->id],
            'code' => ['required', 'string', 'max:16', 'unique:companies,code,' . $company->id],
        ]);

        $company->update($request->only(['name', 'code']));
        ToastHelper::addToast($company->name . ' updated.', 'Update Company');
        return redirect()->route('admin.company.index');
    }
    public function destroy(Request $request, Company $company)
    {
        
        if (!$company->isDestroyable($reason)) {
            
            ToastHelper::addToast('Unable to delete ' . $company->name . '.', 'Delete Company', 'danger');
            return redirect()->route('admin.company.index');
        }

        $company->forceDelete();
        ToastHelper::addToast($company->name . ' deleted.', 'Delete Company', 'danger');
        return redirect()->route('admin.company.index');
    }
    public function datatable(Request $request)
    {
        $dataset = new CompanyDataset();
        return $dataset->datatable($request);
    }
}
