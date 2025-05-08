import React, { useState, useEffect } from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function Ujian({ ujian, questions, durasi }) {
    const [currentQuestion, setCurrentQuestion] = useState(0);
    const [answers, setAnswers] = useState({});
    const [timeLeft, setTimeLeft] = useState(durasi * 60); // Durasi dalam detik
    const [showSidebar, setShowSidebar] = useState(false); // State untuk menampilkan atau menyembunyikan sidebar
    const { post } = useForm();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    
    useEffect(() => {
        const savedAnswers = localStorage.getItem(`ujian-${ujian.id}-answers`);
        if (savedAnswers) {
            setAnswers(JSON.parse(savedAnswers));
        }
    }, [ujian.id]);

    // menyimpan jawaban ke localStorage setiap kali ada perubahan
    useEffect(() => {
        localStorage.setItem(`ujian-${ujian.id}-answers`, JSON.stringify(answers));
    }, [answers, ujian.id]);

    useEffect(() => {
        // Ambil waktu tersisa dari localStorage jika ada
        const savedTimeLeft = localStorage.getItem(`ujian-${ujian.id}-timeLeft`);
        if (savedTimeLeft) {
            setTimeLeft(parseInt(savedTimeLeft, 10)); // Pastikan waktu diubah menjadi angka
        }
    }, [ujian.id]);

    useEffect(() => {
        // Simpan waktu tersisa ke localStorage setiap detik
        const timer = setInterval(() => {
            setTimeLeft((prevTime) => {
                if (prevTime <= 1) {
                    clearInterval(timer);
                    handleTimeUp();
                    return 0;
                }
                const updatedTime = prevTime - 1;
                localStorage.setItem(`ujian-${ujian.id}-timeLeft`, updatedTime); // Simpan waktu ke local
                return updatedTime;
            });
        }, 1000);

        return () => clearInterval(timer); 
    }, [ujian.id]);

    const handleTimeUp = () => {
        alert('Waktu habis! Jawaban Anda akan disubmit.');
        submitAnswers();
    };

    const handleAnswerChange = (questionId, answer) => {
        const updatedAnswers = { ...answers, [questionId]: answer };
        console.log('Jawaban diperbarui:', updatedAnswers); // Debugg
        setAnswers(updatedAnswers);
    };

    const handleMultiAnswerChange = (questionId, option) => {
        const selectedAnswers = answers[questionId] || [];
        const uppercasedOption = option.toUpperCase(); // Ubah ke uppercase

        // Tambahkan opsi baru hanya jika belum ada di array
        const updatedAnswers = selectedAnswers.includes(uppercasedOption)
            ? selectedAnswers.filter((item) => item !== uppercasedOption)
            : [...selectedAnswers, uppercasedOption]; 

        
        setAnswers({
            ...answers,
            [questionId]: updatedAnswers,
        });
    };

    const submitAnswers = () => {
        // Filter semua jawaban siswa untuk memastikan uppercase dan tanpa duplikasi
        const filteredAnswers = Object.fromEntries(
            Object.entries(answers).map(([questionId, answer]) => {
                if (Array.isArray(answer)) {
                    // Untuk opsi pilihan (array), ubah semua ke uppercase dan hapus duplikasi
                    return [questionId, [...new Set(answer.map((item) => item.toUpperCase()))]];
                }
                // Untuk jawaban tunggal, langsung ubah ke uppercase
                return [questionId, answer.toUpperCase()];
            })
        );

        const encodedAnswers = encodeURIComponent(JSON.stringify(filteredAnswers));
        console.log('Jawaban yang dikirim:', filteredAnswers); // Debugging

        // Kirim data melalui URL
        window.location.href = `/ujian/${ujian.id}/submit/${encodedAnswers}`;
    };

    const nextQuestion = () => {
        if (currentQuestion < questions.length - 1) {
            setCurrentQuestion(currentQuestion + 1);
        }
    };

    const prevQuestion = () => {
        if (currentQuestion > 0) {
            setCurrentQuestion(currentQuestion - 1);
        }
    };

    const endExam = () => {
        if (confirm('Apakah Anda yakin ingin mengakhiri ujian?')) {
            submitAnswers();
        }
    };

    const formatTime = (time) => {
        const minutes = Math.floor(time / 60);
        const seconds = time % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    const renderQuestion = (question) => {
        if (question.tipe_pertanyaan === 'pilihan_ganda') {
            return (
                <div>
                    <h3 className="text-lg font-semibold">{question.pertanyaan}</h3>
                    {['A', 'B', 'C', 'D', 'E'].map((option) => {
                        const pilihan = question[`pilihan_${option.toLowerCase()}`]; // Ambil teks dari backend
                        const pilihanGambar = question[`pilihan_${option.toLowerCase()}_gambar`]; // Ambil gambar dari backend
                        if (!pilihan && !pilihanGambar) return null;

                        return (
                            <div key={option} className="flex items-center gap-2">
                                <label className="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name={`question-${question.id}`}
                                        value={option} // Gunakan uppercase untuk value
                                        checked={answers[question.id] === option}
                                        onChange={() => handleAnswerChange(question.id, option)}
                                    />
                                    {/* Tampilkan gambar jika ada, jika tidak tampilkan teks */}
                                    {pilihanGambar ? (
                                        <img
                                            src={`/storage/${pilihanGambar}`} // Pastikan jalur gambar benar
                                            alt={`Pilihan ${option}`}
                                            className="w-40 h-40 object-cover border border-gray-300 rounded"
                                        />
                                    ) : (
                                        <span>{pilihan}</span>
                                    )}
                                </label>
                            </div>
                        );
                    })}
                </div>
            );
        } else if (question.tipe_pertanyaan === 'opsi_jawaban') {
            return (
                <div>
                    <h3 className="text-lg font-semibold">{question.pertanyaan}</h3>
                    {['A', 'B', 'C', 'D', 'E'].map((option) => {
                        const pilihan = question[`pilihan_${option.toLowerCase()}`]; // Ambil teks dari backend
                        const pilihanGambar = question[`pilihan_${option.toLowerCase()}_gambar`]; // Ambil gambar dari backend
                        if (!pilihan && !pilihanGambar) return null;

                        return (
                            <div key={option} className="flex items-center gap-2">
                                <label className="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        name={`question-${question.id}`}
                                        value={option} // Gunakan uppercase untuk value
                                        checked={answers[question.id]?.includes(option) || false}
                                        onChange={() => handleMultiAnswerChange(question.id, option)}
                                    />
                                    {/* Tampilkan gambar jika ada, jika tidak tampilkan teks */}
                                    {pilihanGambar ? (
                                        <img
                                            src={`/storage/${pilihanGambar}`} // Pastikan jalur gambar benar
                                            alt={`Pilihan ${option}`}
                                            className="w-40 h-40 object-cover border border-gray-300 rounded"
                                        />
                                    ) : (
                                        <span>{pilihan}</span>
                                    )}
                                </label>
                            </div>
                        );
                    })}
                </div>
            );
        } else if (question.tipe_pertanyaan === 'benar_salah') {
            return (
                <div>
                    <h3 className="text-lg font-semibold">{question.pertanyaan}</h3>
                    <div>
                        <label>
                            <input
                                type="radio"
                                name={`question-${question.id}`}
                                value="true"
                                checked={answers[question.id] === 'true'}
                                onChange={() => handleAnswerChange(question.id, 'true')}
                            />
                            Benar
                        </label>
                    </div>
                    <div>
                        <label>
                            <input
                                type="radio"
                                name={`question-${question.id}`}
                                value="false"
                                checked={answers[question.id] === 'false'}
                                onChange={() => handleAnswerChange(question.id, 'false')}
                            />
                            Salah
                        </label>
                    </div>
                </div>
            );
        }
    };

    return (
        <div className="flex flex-col lg:flex-row gap-4 p-4">
            <Head title={`Ujian: ${ujian.nama}`} />

            {/* Container Soal dengan Timer */}
            <div className="flex-1 p-4 bg-white shadow-md rounded-lg border border-gray-200">
                {/* Header Soal dengan Timer */}
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-semibold">{`Soal ${currentQuestion + 1} dari ${questions.length}`}</h3>
                    <div className="text-gray-700 text-lg font-semibold">
                        Sisa Waktu: <span className="text-gray-900">{formatTime(timeLeft)}</span>
                    </div>
                </div>

                {/* Soal */}
                {renderQuestion(questions[currentQuestion])}
            </div>

            {/* Sidebar untuk Nomor Soal */}
            <div
                className={`fixed lg:static top-0 right-0 h-full lg:h-auto bg-white shadow-lg lg:shadow-none border lg:border-none transition-transform duration-300 ${
                    showSidebar ? 'translate-x-0' : 'translate-x-full'
                } lg:translate-x-0 w-64 lg:w-auto`}
            >
                <div className="p-4">
                    <h3 className="text-lg font-semibold mb-4">Nomor Soal</h3>
                    <div className="grid grid-cols-5 gap-2">
                        {questions.map((question, index) => {
                            const isAnswered = answers[question.id] && answers[question.id].length > 0; // Periksa apakah soal sudah dijawab
                            return (
                                <button
                                    key={index}
                                    className={`p-2 rounded ${
                                        currentQuestion === index
                                            ? 'bg-blue-600 text-white' // Warna biru untuk soal yang sedang aktif
                                            : isAnswered
                                            ? 'bg-blue-400 text-white' // Warna hijau untuk soal yang sudah dijawab
                                            : 'bg-gray-200 text-gray-800' // Warna default untuk soal yang belum dijawab
                                    }`}
                                    onClick={() => setCurrentQuestion(index)}
                                >
                                    {index + 1}
                                </button>
                            );
                        })}
                    </div>
                </div>
            </div>

            {/* Tombol untuk Menampilkan/Menyembunyikan Sidebar */}
            <button
                className="lg:hidden fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg"
                onClick={() => setShowSidebar(!showSidebar)}
            >
                {showSidebar ? 'Tutup Soal' : 'Lihat Soal'}
            </button>

            {/* Tombol Navigasi */}
            <div className="flex lg:fixed lg:bottom-4 lg:right-4 lg:flex-col lg:gap-2 mt-4 lg:mt-0">
                <button
                    className="px-4 py-2 bg-gray-600 text-white rounded disabled:bg-gray-300"
                    onClick={prevQuestion}
                    disabled={currentQuestion === 0}
                >
                    Sebelumnya
                </button>
                {currentQuestion === questions.length - 1 ? (
                    <button
                        className="px-4 py-2 bg-red-600 text-white rounded"
                        onClick={endExam}
                    >
                        Akhiri Ujian
                    </button>
                ) : (
                    <button
                        className="px-4 py-2 bg-blue-600 text-white rounded"
                        onClick={nextQuestion}
                    >
                        Selanjutnya
                    </button>
                )}
            </div>
        </div>
    );
}

