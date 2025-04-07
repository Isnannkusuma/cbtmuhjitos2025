import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ aksesUjian, hasilUjian }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard Siswa
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            Selamat datang di Dashboard!
                        </div>
                    </div>

                    {/* Card Container */}
                    <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {aksesUjian.map((item, index) => (
                            <div
                                key={index}
                                className="p-4 bg-white shadow-md rounded-lg border border-gray-200"
                            >
                                <h3 className="text-lg font-semibold text-gray-800">
                                    Ujian: {item.ujian.nama}
                                </h3>
                                <p className="text-gray-600">
                                    Status: {item.status === 'not_started'
                                        ? 'Not Started'
                                        : item.status === 'in_progress'
                                        ? 'In Progress'
                                        : item.status === 'completed'
                                        ? 'Completed'
                                        : 'Can Start'}
                                </p>
                                <button
                                    className={`mt-4 px-4 py-2 text-white rounded ${
                                        item.status === 'can_start'
                                            ? 'bg-blue-600 hover:bg-blue-700'
                                            : 'bg-gray-400 cursor-not-allowed'
                                    }`}
                                    disabled={item.status !== 'can_start'}
                                    onClick={() => {
                                        if (item.status === 'can_start') {
                                            window.location.href = `/ujian/${item.id_ujian}`;
                                        }
                                    }}
                                >
                                    {item.status === 'can_start'
                                        ? 'Kerjakan Sekarang'
                                        : item.status === 'completed'
                                        ? 'Selesai'
                                        : 'Tidak Tersedia'}
                                </button>
                            </div>
                        ))}
                    </div>

                    {/* Hasil Ujian */}
                    <div className="mt-6">
                        <h2 className="text-xl font-semibold text-gray-800 mb-4">Hasil Ujian</h2>
                        {hasilUjian.length > 0 ? (
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                {hasilUjian.map((hasil, index) => (
                                    <div
                                        key={index}
                                        className="p-4 bg-white shadow-md rounded-lg border border-gray-200"
                                    >
                                        <h3 className="text-lg font-semibold text-gray-800">
                                            Ujian: {hasil.ujian.nama}
                                        </h3>
                                        <p className="text-gray-600">Jumlah Soal: {hasil.jumlah_soal}</p>
                                        <p className="text-gray-600">Benar: {hasil.jumlah_benar}</p>
                                        <p className="text-gray-600">Salah: {hasil.jumlah_salah}</p>
                                        <p className="text-gray-600">Nilai: {hasil.nilai}</p>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-gray-600">Belum ada hasil ujian. Silakan mulai ujian terlebih dahulu.</p>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}