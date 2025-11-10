import React, { useState, useEffect, useRef } from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function Ujian({ ujian, questions, durasi }) {
    let realDurasi = durasi;
    if (ujian.nama && ujian.nama.toLowerCase().includes('uas')) {
        realDurasi = 90;
    } else if (ujian.nama && ujian.nama.toLowerCase().includes('uts')) {
        realDurasi = 60;
    }

    const [currentQuestion, setCurrentQuestion] = useState(0);
    const [answers, setAnswers] = useState({});
    const [timeLeft, setTimeLeft] = useState(realDurasi * 60);
    const [showSidebar, setShowSidebar] = useState(false);
    const [zoomedImage, setZoomedImage] = useState(null);
    const { post } = useForm();
    const timerRef = useRef(null);

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    useEffect(() => {
        const savedAnswers = localStorage.getItem(`ujian-${ujian.id}-answers`);
        if (savedAnswers) {
            setAnswers(JSON.parse(savedAnswers));
        }
    }, [ujian.id]);

    useEffect(() => {
        localStorage.setItem(`ujian-${ujian.id}-answers`, JSON.stringify(answers));
    }, [answers, ujian.id]);

    useEffect(() => {
        const savedAnswers = localStorage.getItem(`ujian-${ujian.id}-answers`);
        const savedTimeLeft = localStorage.getItem(`ujian-${ujian.id}-timeLeft`);
        const maxTime = realDurasi * 60;

        if (!savedAnswers) {
            setTimeLeft(maxTime);
            localStorage.setItem(`ujian-${ujian.id}-timeLeft`, maxTime);
            return;
        }

        if (savedTimeLeft) {
            const parsedTime = parseInt(savedTimeLeft, 10);
            if (parsedTime > 0 && parsedTime <= maxTime) {
                setTimeLeft(parsedTime);
            } else {
                setTimeLeft(maxTime);
                localStorage.setItem(`ujian-${ujian.id}-timeLeft`, maxTime);
            }
        } else {
            setTimeLeft(maxTime);
            localStorage.setItem(`ujian-${ujian.id}-timeLeft`, maxTime);
        }
    }, [ujian.id, realDurasi]);

    useEffect(() => {
        timerRef.current = setInterval(() => {
            setTimeLeft((prevTime) => {
                if (prevTime <= 1) {
                    clearInterval(timerRef.current);
                    handleTimeUp();
                    return 0;
                }
                const updatedTime = prevTime - 1;
                localStorage.setItem(`ujian-${ujian.id}-timeLeft`, updatedTime);
                return updatedTime;
            });
        }, 1000);

        return () => clearInterval(timerRef.current);
    }, [ujian.id]);

    const handleTimeUp = () => {
        clearInterval(timerRef.current);
        localStorage.removeItem(`ujian-${ujian.id}-answers`);
        localStorage.removeItem(`ujian-${ujian.id}-timeLeft`);
        alert('Waktu habis! Jawaban Anda akan disubmit.');
        submitAnswers();
    };

    const handleAnswerChange = (questionId, answer) => {
        const updatedAnswers = { ...answers, [questionId]: answer };
        console.log('Jawaban diperbarui:', updatedAnswers);
        setAnswers(updatedAnswers);
    };

    const handleMultiAnswerChange = (questionId, option) => {
        const selectedAnswers = answers[questionId] || [];
        const uppercasedOption = option.toUpperCase();

        const updatedAnswers = selectedAnswers.includes(uppercasedOption)
            ? selectedAnswers.filter((item) => item !== uppercasedOption)
            : [...selectedAnswers, uppercasedOption];

        setAnswers({
            ...answers,
            [questionId]: updatedAnswers,
        });
    };

    const submitAnswers = () => {
        clearInterval(timerRef.current);

        const filteredAnswers = Object.fromEntries(
            Object.entries(answers).map(([questionId, answer]) => {
                const question = questions.find(q => q.id == questionId);
                if (!question) return [questionId, answer];

                if (question.tipe_pertanyaan === 'opsi_jawaban') {
                    return [questionId, [...new Set(answer.map((item) => item.toUpperCase()))]];
                } else if (question.tipe_pertanyaan === 'pilihan_ganda') {
                    return [questionId, answer.toUpperCase()];
                } else if (question.tipe_pertanyaan === 'benar_salah') {
                    return [questionId, answer.toUpperCase()];
                }
                return [questionId, answer];
            })
        );

        const encodedAnswers = encodeURIComponent(JSON.stringify(filteredAnswers));
        console.log('Jawaban yang dikirim:', filteredAnswers);

        localStorage.removeItem(`ujian-${ujian.id}-answers`);
        localStorage.removeItem(`ujian-${ujian.id}-timeLeft`);

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
        console.log('gambar_soal:', question.gambar_soal);
        return (
            <div>
                <h3 className="text-lg font-semibold mb-4 whitespace-pre-line text-justify"
                style={{ textAlign: 'justify', lineHeight: '1.8' }}
                >
                    {question.pertanyaan}
                </h3>
                {question.gambar_soal && (
                    <img
                        src={`/storage/${question.gambar_soal}`}
                        alt="Gambar Soal"
                        className="w-80 h-80 object-contain border border-gray-300 rounded my-2 cursor-zoom-in"
                        onClick={() => setZoomedImage(`/storage/${question.gambar_soal}`)}
                    />
                )}
                {question.tipe_pertanyaan === 'pilihan_ganda' && (
                    ['A', 'B', 'C', 'D', 'E'].map((option) => {
                        const pilihan = question[`pilihan_${option.toLowerCase()}`];
                        const pilihanGambar = question[`pilihan_${option.toLowerCase()}_gambar`];
                        if (!pilihan && !pilihanGambar) return null;
                        return (
                            <div key={option} className="flex items-center gap-2">
                                <label className="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name={`question-${question.id}`}
                                        value={option}
                                        checked={answers[question.id] === option}
                                        onChange={() => handleAnswerChange(question.id, option)}
                                    />
                                    {pilihanGambar ? (
                                        <img
                                            src={`/storage/${pilihanGambar}`}
                                            alt={`Pilihan ${option}`}
                                            className="w-40 h-40 object-cover border border-gray-300 rounded cursor-zoom-in"
                                            onClick={e => {
                                                e.stopPropagation();
                                                setZoomedImage(`/storage/${pilihanGambar}`);
                                            }}
                                        />
                                    ) : (
                                        <span>{pilihan}</span>
                                    )}
                                </label>
                            </div>
                        );
                    })
                )}
                {question.tipe_pertanyaan === 'opsi_jawaban' && (
                    ['A', 'B', 'C', 'D', 'E'].map((option) => {
                        const pilihan = question[`pilihan_${option.toLowerCase()}`];
                        const pilihanGambar = question[`pilihan_${option.toLowerCase()}_gambar`];
                        if (!pilihan && !pilihanGambar) return null;
                        return (
                            <div key={option} className="flex items-center gap-2">
                                <label className="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        name={`question-${question.id}`}
                                        value={option}
                                        checked={answers[question.id]?.includes(option) || false}
                                        onChange={() => handleMultiAnswerChange(question.id, option)}
                                    />
                                    {pilihanGambar ? (
                                        <img
                                            src={`/storage/${pilihanGambar}`}
                                            alt={`Pilihan ${option}`}
                                            className="w-40 h-40 object-cover border border-gray-300 rounded cursor-zoom-in"
                                            onClick={e => {
                                                e.stopPropagation();
                                                setZoomedImage(`/storage/${pilihanGambar}`);
                                            }}
                                        />
                                    ) : (
                                        <span>{pilihan}</span>
                                    )}
                                </label>
                            </div>
                        );
                    })
                )}
                {question.tipe_pertanyaan === 'benar_salah' && (
                    ['A', 'B'].map((option) => {
                        const pilihan = question[`pilihan_${option.toLowerCase()}`];
                        const pilihanGambar = question[`pilihan_${option.toLowerCase()}_gambar`];
                        if (!pilihan && !pilihanGambar) return null;
                        return (
                            <div key={option} className="flex items-center gap-2">
                                <label className="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name={`question-${question.id}`}
                                        value={option}
                                        checked={answers[question.id] === option}
                                        onChange={() => handleAnswerChange(question.id, option)}
                                    />
                                    {pilihanGambar ? (
                                        <img
                                            src={`/storage/${pilihanGambar}`}
                                            alt={`Pilihan ${option}`}
                                            className="w-40 h-40 object-cover border border-gray-300 rounded cursor-zoom-in"
                                            onClick={e => {
                                                e.stopPropagation();
                                                setZoomedImage(`/storage/${pilihanGambar}`);
                                            }}
                                        />
                                    ) : (
                                        <span>{pilihan}</span>
                                    )}
                                </label>
                            </div>
                        );
                    })
                )}
            </div>
        );
    };

    return (
        <div className="flex flex-col lg:flex-row gap-4 p-4">
            <Head title={`Ujian: ${ujian.nama}`} />
            {zoomedImage && (
                <div className="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" onClick={() => setZoomedImage(null)}>
                    <img
                        src={zoomedImage}
                        alt="Zoomed"
                        className="max-w-full max-h-full rounded shadow-lg border-4 border-white"
                        onClick={e => e.stopPropagation()}
                    />
                </div>
            )}
            <div className="flex-1 p-4 bg-white shadow-md rounded-lg border border-gray-200">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-semibold">{`Soal ${currentQuestion + 1} dari ${questions.length}`}</h3>
                    <div className="text-gray-700 text-lg font-semibold">
                        Sisa Waktu: <span className="text-gray-900">{formatTime(timeLeft)}</span>
                    </div>
                </div>
                {renderQuestion(questions[currentQuestion])}
            </div>
            <div
                className={`fixed lg:static top-0 right-0 h-full lg:h-auto bg-white shadow-lg lg:shadow-none border lg:border-none transition-transform duration-300 ${
                    showSidebar ? 'translate-x-0' : 'translate-x-full'
                } lg:translate-x-0 w-64 lg:w-auto`}
            >
                <div className="p-4">
                    <h3 className="text-lg font-semibold mb-4">Nomor Soal</h3>
                    <div className="grid grid-cols-5 gap-2">
                        {questions.map((question, index) => {
                            const isAnswered = answers[question.id] && answers[question.id].length > 0;
                            return (
                                <button
                                    key={index}
                                    className={`p-2 rounded ${
                                        currentQuestion === index
                                            ? 'bg-blue-600 text-white'
                                            : isAnswered
                                            ? 'bg-blue-400 text-white'
                                            : 'bg-gray-200 text-gray-800'
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
            <button
                className="lg:hidden fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg"
                onClick={() => setShowSidebar(!showSidebar)}
            >
                {showSidebar ? 'Tutup Soal' : 'Lihat Soal'}
            </button>
            <div className="flex flex-row gap-2 lg:fixed lg:bottom-4 lg:right-4 mt-4 lg:mt-0">
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

