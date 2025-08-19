<?php

namespace App\Helpers;

use App\Models\Posisi;

class Approval
{
    // Konstanta mapping jabatan (hindari magic numbers)
    public const JABATAN_BOD = 1;
    public const JABATAN_DEPT_HEAD = 2;
    public const JABATAN_SECTION_HEAD = 4;
    public const JABATAN_LEADER = 5;

    protected static $array = [];
    protected static $atasan = [];
    protected static $member = [];
    protected static $response = false;

    // ===== Utility dasar =====
    public static function GetParentPosisi($posisi)
    {
        self::$array = [];
        if ($posisi->parent_id !== 0) {
            $parent = Posisi::find($posisi->parent_id);
            self::$array = array_merge(self::$array, self::GetParentPosisi($parent));
        }
        self::$array[] = $posisi->parent_id;
        return self::$array;
    }

    public static function GetMemberPosisi($posisi)
    {
        self::$array = [];
        foreach ($posisi as $ps) {
            if ($ps->children) {
                self::$array = array_merge(self::$array, self::GetMemberPosisi($ps->children));
            }
            self::$array[] = $ps->id_posisi;
        }
        return self::$array;
    }

    // ===== Cek atasan (sesuai struktur jabatan saat ini) =====
    public static function HasDirector($posisi): bool
    {
        self::$response = false;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_BOD) {
                            self::$response = true;
                        }
                    }
                }
            }
        }
        return self::$response;
    }

    public static function HasDepartmentHead($posisi): bool
    {
        self::$response = false;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_DEPT_HEAD) {
                            self::$response = true;
                        }
                    }
                }
            }
        }
        return self::$response;
    }

    public static function HasSectionHead($posisi): bool
    {
        self::$response = false;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_SECTION_HEAD) {
                            self::$response = true;
                        }
                    }
                }
            }
        }
        return self::$response;
    }

    public static function HasLeader($posisi): bool
    {
        self::$response = false;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_LEADER) {
                            self::$response = true;
                        }
                    }
                }
            }
        }
        return self::$response;
    }

    /**
     * Ambil SATU karyawan yang berposisi sebagai Leader di atas posisi user.
     * Return: \App\Models\Karyawan|null
     */
    public static function GetLeader($posisi)
    {
        if ($posisi) {
            foreach ($posisi as $pos) {
                $parent_ids = self::GetParentPosisi($pos);
                foreach ($parent_ids as $parent_id) {
                    if ($parent_id !== 0) {
                        $parent_pos = Posisi::find($parent_id);
                        if ($parent_pos && (int) $parent_pos->jabatan_id === self::JABATAN_LEADER) {
                            // relasi Posisi -> karyawan adalah many-to-many, ambil satu saja
                            return $parent_pos->karyawan()->first();
                        }
                    }
                }
            }
        }
        return null;
    }

    public static function GetDirector($posisi)
    {
        if (!$posisi)
            return null;

        foreach ($posisi as $pos) {
            $parent_ids = self::GetParentPosisi($pos);
            foreach ($parent_ids as $parent_id) {
                if ($parent_id === 0)
                    continue;

                $parent_pos = \App\Models\Posisi::with([
                    'karyawan' => function ($q) {
                        $q->select('karyawans.id_karyawan', 'karyawans.nama');
                    }
                ])->find($parent_id);

                if ($parent_pos && (int) $parent_pos->jabatan_id === 1 && $parent_pos->karyawan()->exists()) {
                    // relasi karyawan() return Collection
                    return optional($parent_pos->karyawan->first())->nama;
                }
            }
        }
        return null;
    }

    public static function GetSectionHead($posisiCollection): ?\App\Models\Karyawan
    {
        if (!$posisiCollection instanceof \Illuminate\Support\Collection) {
            return null;
        }

        foreach ($posisiCollection as $pos) {
            $parentIds = self::GetParentPosisi($pos);
            foreach ($parentIds as $pid) {
                if ($pid === 0)
                    continue;

                $parent = \App\Models\Posisi::with('karyawan')->find($pid);
                if ($parent && (int) $parent->jabatan_id === self::JABATAN_SECTION_HEAD) {
                    // ✅ return SATU karyawan saja
                    return $parent->karyawan()->first();
                }
            }
        }
        return null;
    }

    // ===== Versi untuk cuti (samakan mapping!) =====
    public static function HasDirectorCuti($posisi)
    {
        $found = null;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_BOD) {
                            $found = $parent_id;
                            break 2;
                        }
                    }
                }
            }
        }
        return $found;
    }

    public static function HasDepartmentHeadCuti($posisi)
    {
        $found = null;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_DEPT_HEAD) {
                            $found = $parent_id;
                            break 2;
                        }
                    }
                }
            }
        }
        return $found;
    }

    public static function HasSectionHeadCuti($posisi)
    {
        $found = null;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_SECTION_HEAD) {
                            $found = $parent_id;
                            break 2;
                        }
                    }
                }
            }
        }
        return $found;
    }

    public static function HasLeaderCuti($posisi)
    {
        $found = null;
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if ($p && (int) $p->jabatan_id === self::JABATAN_LEADER) {
                            $found = $parent_id;
                        }
                    }
                }
            }
        }
        return $found;
    }

    // ===== List atasan/member (retained keys, tapi tanpa division_head) =====
    public static function ListAtasan($posisi)
    {
        self::$atasan = ['leader' => null, 'section_head' => null, 'department_head' => null, 'division_head' => null, 'director' => null];
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetParentPosisi($pos) as $parent_id) {
                    if ($parent_id !== 0) {
                        $p = Posisi::find($parent_id);
                        if (!$p)
                            continue;

                        switch ((int) $p->jabatan_id) {
                            case self::JABATAN_LEADER:
                                self::$atasan['leader'] = $parent_id;
                                break;
                            case self::JABATAN_SECTION_HEAD:
                                self::$atasan['section_head'] = $parent_id;
                                break;
                            case self::JABATAN_DEPT_HEAD:
                                self::$atasan['department_head'] = $parent_id;
                                break;
                            case self::JABATAN_BOD:
                                self::$atasan['director'] = $parent_id;
                                break;
                            default:
                                // id 3 tidak dipakai; biarkan
                                break;
                        }
                    }
                }
            }
        }
        return self::$atasan;
    }

    public static function ListMember($posisi)
    {
        self::$member = ['leader' => null, 'section_head' => null, 'department_head' => null, 'division_head' => null, 'director' => null];
        if ($posisi) {
            foreach ($posisi as $pos) {
                foreach (self::GetMemberPosisi($pos) as $member_id) {
                    if ($member_id !== 0) {
                        $p = Posisi::find($member_id);
                        if (!$p)
                            continue;

                        switch ((int) $p->jabatan_id) {
                            case self::JABATAN_LEADER:
                                self::$member['leader'] = $member_id;
                                break;
                            case self::JABATAN_SECTION_HEAD:
                                self::$member['section_head'] = $member_id;
                                break;
                            case self::JABATAN_DEPT_HEAD:
                                self::$member['department_head'] = $member_id;
                                break;
                            case self::JABATAN_BOD:
                                self::$member['director'] = $member_id;
                                break;
                            default:
                                // id 3 tidak dipakai
                                break;
                        }
                    }
                }
            }
        }
        return self::$member;
    }

    // Tetap biarkan fungsi ini karena mungkin dipakai tempat lain
    public static function ApprovalDeptWithPlantHead($parent_id, $organisasi_id)
    {
        self::$atasan = [
            'leader' => null,
            'section_head' => null,
            'department_head' => null,
            'division_head' => null, // legacy
            'plant_head' => optional(
                Posisi::where('jabatan_id', self::JABATAN_DEPT_HEAD)
                    ->where('organisasi_id', auth()->user()->organisasi_id)
                    ->where('nama', 'ILIKE', 'PLANT%')
                    ->first()
            )->id_posisi,
            'director' => null
        ];

        $posisi = Posisi::where('parent_id', $parent_id)->get();
        foreach ($posisi as $pos) {
            foreach (self::GetParentPosisi($pos) as $pid) {
                if ($pid !== 0) {
                    $p = Posisi::find($pid);
                    if (!$p)
                        continue;

                    switch ((int) $p->jabatan_id) {
                        case self::JABATAN_LEADER:
                            self::$atasan['leader'] = $pid;
                            break;
                        case self::JABATAN_SECTION_HEAD:
                            self::$atasan['section_head'] = $pid;
                            break;
                        case self::JABATAN_DEPT_HEAD:
                            self::$atasan['department_head'] = $pid;
                            break;
                        case self::JABATAN_BOD:
                            self::$atasan['director'] = $pid;
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        return self::$atasan;
    }
}
