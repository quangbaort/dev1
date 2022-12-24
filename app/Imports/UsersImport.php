<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Repositories\UserRepository;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use App\Helpers\AppHelpers;
use App\Helpers\Facades\Helper;


class UsersImport implements ToCollection, WithStartRow, WithChunkReading, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $organization;
    protected $userRepository;
    protected $company;
    protected $appHelpers;
    public $count = ['users' => 0, 'company' => 0];

    public function __construct($organization, UserRepository $userRepository)
    {
        $this->organization = $organization;
        $this->userRepository = $userRepository;
        $this->appHelpers = new AppHelpers();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            $company = $this->company($row);
            $user = $this->user($row);
        }
    }
    public function rules(): array
    {
        return [
            '*.0' => 'required|string|max:255',
            '*.1' => 'required|string|max:255',
            '*.2' => 'required|digits:7',
            '*.3' => 'required|string|max:255',
            '*.4' => 'required|string|max:255',
            '*.5' => 'required|string|max:255',
            '*.9' => 'required|string|max:255',
            '*.10' => 'required|string|max:255',
            '*.11' => 'required|email|max:255',
            '*.12' => 'required|string|max:255',
        ];
    }

    public function startRow(): int
    {
      return 2;
    }

    // public function headingRow(): int
    // {
    //   return 1;
    // }

    public function user($row)
    {
        $createdUsers = $this->organization->users()->where('email', $row[11])->first();
        if(!is_null($createdUsers)) {
            return false;
        }else {
            $user = $this->userRepository->where('email', $row[11])->first();
            if(!is_null($user)){
                $this->validateStoreUser();
                $organId = Helper::organId();
                $user->organizations()->attach($organId, ['role' => USER_ROLE]);
                $user->companies()->attach($this->company[0]->id);
                $this->count['users']++;
                return $user;
            }else {
                $dataUser[] = [
                    'name' => $row[9],
                    'email' => $row[11],
                    'password' => Hash::make($row[12]),
                    'name_kana' => $row[10],
                ];
                $insertCompany = collect($dataUser);
                return $this->inSertChunkSize($insertCompany, 'users');
            }
        }
    }

    public function company($row)
    {
        $company = $this->organization->companies()->where(['name' => $row[0], 'zip_code' => $row[2]])->first();
        if (!is_null($company)) {
            $this->company = [$company];
            return;
        }
        $dataCompany[] = [
            'name' => $row[0],
            'name_kana' => $row[1],
            'zip_code' => $row[2],
            'prefecture' => $this->appHelpers->getPrefectureCode($row[3]),
            'city' => $row[4],
            'street' => $row[5],
            'building' => $row[6],
            'tel' => $row[7],
            'fax' => $row[8],
        ];
        $insertCompany = collect($dataCompany);
        return $this->inSertChunkSize($insertCompany, 'company');
    }
    public function inSertChunkSize($chunks, $type = 'company')
    {
        return $chunks->chunk(500)->each(function ($chunk) use ($type) {
            if($type == 'company') {
                try{
                    $company = $this->organization->companies()->createMany($chunk->toArray());
                    $this->company = $company;
                    $this->count['company']++;
                }catch (\Exception $e) {
                    return false;
                }
            }
            elseif($type == 'users') {
                try{
                    $this->validateStoreUser();
                    $user = $this->organization->users()->createMany($chunk->toArray());
                    $user[0]->companies()->attach($this->company[0]->id, ['organization_id' => $this->organization->id]);
                    $this->count['users']++;
                }catch (\Exception $e) {
                    return false;
                }
            }
        });
    }

    public function onFailure(Failure ...$failures)
    {
        $this->errors = $failures;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function validateStoreUser()
    {
        $createdUsers = $this->organization->users()->select('email')->get();
        abort_if(
            $this->organization->account_limit <= $createdUsers->count(),
            422,
            trans('message.account_limit')
        );
        return;
    }

}