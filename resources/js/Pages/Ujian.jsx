import React, { useState, useEffect } from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function Ujian({ ujian, questions, durasi }) {
    const [currentQuestion, setCurrentQuestion] = useState(0);
    const [answers, setAnswers] = useState({});
    const [timeLeft, setTimeLeft] = useState(durasi * 60); // Durasi dalam detik
    const { post } = useForm();

    useEffect(() => {
        const timer = setInterval(() => {
            setTimeLeft((prevTime) => {
                if (prevTime <= 1) {
                    clearInterval(timer);
                    handleTimeUp();
                    return 0;
                }
                return prevTime - 1;
            });
        }, 1000);

        return () => clearInterval(timer);
    }, []);

    const handleTimeUp = () => {
        alert('Waktu habis! Jawaban Anda akan disubmit.');
        post(`/ujian/${ujian.id}/submit`, { answers });
    };

    const handleAnswerChange = (questionId, answer) => {
        setAnswers({ ...answers, [questionId]: answer });
    };

    const handleMultiAnswerChange = (questionId, option) => {
        const selectedAnswers = answers[questionId] || [];
        if (selectedAnswers.includes(option)) {
            setAnswers({
                ...answers,
                [questionId]: selectedAnswers.filter((item) => item !== option),
            });
        } else {
            setAnswers({
                ...answers,
                [questionId]: [...selectedAnswers, option],
            });
        }
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
            post(`/ujian/${ujian.id}/submit`, { answers });
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
                    {['a', 'b', 'c', 'd', 'e'].map((option) => {
                        const pilihan = question[`pilihan_${option}`];
                        const pilihanGambar = question[`pilihan_${option}_gambar`];
                        if (!pilihan && !pilihanGambar) return null;

                        return (
                            <div key={option} className="flex items-center gap-2">
                                <label>
                                    <input
                                        type="radio"
                                        name={`question-${question.id}`}
                                        value={option}
                                        checked={answers[question.id] === option}
                                        onChange={() => handleAnswerChange(question.id, option)}
                                    />
                                    {pilihan && <span>{pilihan}</span>}
                                    {pilihanGambar && (
                                        <img
                                            src={`/storage/${pilihanGambar}`}
                                            alt={`Pilihan ${option}`}
                                            className="w-16 h-16 object-cover"
                                        />
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
                    {['a', 'b', 'c', 'd', 'e'].map((option) => {
                        const pilihan = question[`pilihan_${option}`];
                        const pilihanGambar = question[`pilihan_${option}_gambar`];
                        if (!pilihan && !pilihanGambar) return null;

                        return (
                            <div key={option} className="flex items-center gap-2">
                                <label>
                                    <input
                                        type="checkbox"
                                        name={`question-${question.id}`}
                                        value={option}
                                        checked={answers[question.id]?.includes(option) || false}
                                        onChange={() => handleMultiAnswerChange(question.id, option)}
                                    />
                                    {pilihan && <span>{pilihan}</span>}
                                    {pilihanGambar && (
                                        <img
                                            src={`/storage/${pilihanGambar}`}
                                            alt={`Pilihan ${option}`}
                                            className="w-16 h-16 object-cover"
                                        />
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

            {/* Timer */}
            <div className="mb-4 text-lg font-semibold text-red-600">
                Sisa Waktu: {formatTime(timeLeft)}
            </div>

            {/* Container Soal */}
            <div className="flex-1 p-4 bg-white shadow-md rounded-lg border border-gray-200">
                {renderQuestion(questions[currentQuestion])}
            </div>

            {/* Container Nomor Soal */}
            <div className="w-full lg:w-1/4 p-4 bg-white shadow-md rounded-lg border border-gray-200">
                <h3 className="text-lg font-semibold mb-4">Nomor Soal</h3>
                <div className="grid grid-cols-5 gap-2">
                    {questions.map((_, index) => (
                        <button
                            key={index}
                            className={`p-2 rounded ${
                                currentQuestion === index
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-200 text-gray-800'
                            }`}
                            onClick={() => setCurrentQuestion(index)}
                        >
                            {index + 1}
                        </button>
                    ))}
                </div>
            </div>

            {/* Tombol Navigasi */}
            <div className="flex justify-between mt-4">
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