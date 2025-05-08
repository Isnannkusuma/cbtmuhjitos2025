import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ aksesUjian, hasilUjian }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-bold leading-tight text-gray-800">
                    Dashboard Siswa
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12 bg-gray-100">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-lg sm:rounded-lg">
                        <div className="p-6 text-gray-900 text-center">
                            <h1 className="text-3xl font-bold text-blue-600">
                                Selamat Datang di Dashboard!
                            </h1>
                            <p className="mt-2 text-gray-600">
                                Akses ujian Anda dan lihat hasil ujian di sini.
                            </p>
                        </div>
                    </div>

                    {/* Card Container */}
                    <div className="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {aksesUjian.map((item, index) => (
                            <div
                                key={index}
                                className="p-6 bg-white shadow-md rounded-lg border border-gray-200 hover:shadow-lg transition-shadow duration-300"
                            >
                                <h3 className="text-lg font-semibold text-gray-800">
                                    Nama Ujian: {item.nama_ujian}
                                </h3>
                                <p className="text-gray-600 mt-2">Nama Siswa: {item.nama_siswa}</p>
                                <p className="text-gray-600 mt-1">
                                    Status:{' '}
                                    <span
                                        className={`font-bold ${
                                            item.status === 'not_started'
                                                ? 'text-yellow-500'
                                                : item.status === 'in_progress'
                                                ? 'text-blue-500'
                                                : item.status === 'completed'
                                                ? 'text-green-500'
                                                : 'text-gray-500'
                                        }`}
                                    >
                                        {item.status === 'not_started'
                                            ? 'Belum Dimulai'
                                            : item.status === 'in_progress'
                                            ? 'Sedang Berlangsung'
                                            : item.status === 'completed'
                                            ? 'Selesai'
                                            : 'Dapat Dimulai'}
                                    </span>
                                </p>
                                <button
                                    className={`mt-4 w-full px-4 py-2 text-white rounded-lg font-semibold ${
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
                </div>
            </div>
        </AuthenticatedLayout>
    );
}