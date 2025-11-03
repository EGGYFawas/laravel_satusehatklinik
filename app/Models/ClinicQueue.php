<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class ClinicQueue extends Model
    {
        use HasFactory;

        // [PERBAIKAN] Hapus '$timestamps = false' dan fungsi boot().
        // Kita kembalikan ke cara standar Laravel.
        // Laravel akan otomatis mengelola 'created_at' dan 'updated_at'.

        /**
         * The attributes that are mass assignable.
         */
        protected $fillable = [
            'patient_id',
            'poli_id',
            'doctor_id',
            'registered_by_user_id',
            'queue_number',
            'chief_complaint',
            'patient_relationship',
            'patient_relationship_custom',
            'status',
            'is_follow_up',
            'follow_up_notes',
            'registration_time', // Kita tetap isi manual saat pembuatan
            'call_time',
            'finish_time',
            'cancellation_time',
            'cancellation_actor',
            'cancellation_reason',
            'check_in_time',
        ];

        /**
         * The attributes that should be cast.
         */
        protected $casts = [
            'registration_time' => 'datetime',
            'check_in_time' => 'datetime',
            'call_time' => 'datetime',
            'finish_time' => 'datetime',
            'cancellation_time' => 'datetime',
            'created_at' => 'datetime', // Tambahkan cast untuk timestamp standar
            'updated_at' => 'datetime', // Tambahkan cast untuk timestamp standar
        ];

        // --- Relasi-relasi (tidak ada perubahan) ---
        public function patient() { return $this->belongsTo(Patient::class); }
        public function poli() { return $this->belongsTo(Poli::class); }
        public function doctor() { return $this->belongsTo(Doctor::class); }
        public function registeredBy() { return $this->belongsTo(User::class, 'registered_by_user_id'); }
        public function medicalRecord() { return $this->hasOne(MedicalRecord::class); }
        public function pharmacyQueue() { return $this->hasOne(PharmacyQueue::class); }
    }

