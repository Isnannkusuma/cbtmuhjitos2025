<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoalResource\Pages;
use App\Filament\Resources\SoalResource\RelationManagers;
use App\Models\Soal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Get;
use Filament\Tables\Filters\SelectFilter;

class SoalResource extends Resource
{
    protected static ?string $model = Soal::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Soal';

    protected static ?string $modelLabel = 'Soal';

    protected static ?string $pluralModelLabel = 'Soal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ujian')
                    ->label('Nama Ujian')
                    ->relationship('ujian', 'nama_ujian')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\FileUpload::make('gambar_soal')
                    ->label('Gambar Soal')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpeg', 'image/png'])
                    ->directory('img_soal')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $timestamp = Carbon::now()->format('YmdHis');
                        $namaFile = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

                        return "{$timestamp}-soal-{$namaFile}.{$file->getClientOriginalExtension()}";
                    }),
                Forms\Components\Textarea::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->required(),
                Forms\Components\Select::make('tipe_pertanyaan')
                    ->label('Tipe Pertanyaan')
                    ->options([
                        'pilihan_ganda' => 'Pilihan Ganda',
                        'opsi_jawaban' => 'Opsi Jawaban',
                        'benar_salah' => 'Benar Salah',
                    ])
                    ->default('pilihan_ganda')
                    ->reactive()
                    ->required(),
                Forms\Components\TextInput::make('bobot')
                    ->label('Bobot')
                    ->required(),
                Forms\Components\Select::make('tipe_jawaban')
                    ->label('Tipe Jawaban')
                    ->options(function (Get $get) {
                        return $get('tipe_pertanyaan') === 'benar_salah'
                            ? ['text' => 'Text']
                            : ['text' => 'Text', 'gambar' => 'Gambar'];
                    })
                    ->default('text')
                    ->reactive()
                    ->required(),
                // Pilihan A
                Forms\Components\TextInput::make('pilihan_a')
                    ->label('Pilihan A')
                    ->required()
                    ->default(fn($get) => $get('tipe_pertanyaan') === 'benar_salah' ? 'Benar' : null)
                    ->reactive()
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'gambar'),
                Forms\Components\FileUpload::make('pilihan_a_gambar')
                    ->label('Pilihan A (Gambar)')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                    ->directory('jawaban/pilihan_a')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $timestamp = Carbon::now()->format('YmdHis');
                        $namaFile = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

                        return "{$timestamp}-pilihan_a-{$namaFile}.{$file->getClientOriginalExtension()}";
                    })
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'text'),
                // Pilihan B
                Forms\Components\TextInput::make('pilihan_b')
                    ->label('Pilihan B')
                    ->required()
                    ->default(fn($get) => $get('tipe_pertanyaan') === 'benar_salah' ? 'Salah' : null)
                    ->reactive()
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'gambar'),
                Forms\Components\FileUpload::make('pilihan_b_gambar')
                    ->label('Pilihan B (Gambar)')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                    ->directory('jawaban/pilihan_b')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $timestamp = Carbon::now()->format('YmdHis');
                        $namaFile = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

                        return "{$timestamp}-pilihan_b-{$namaFile}.{$file->getClientOriginalExtension()}";
                    })
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'text'),
                // Pilihan C
                Forms\Components\TextInput::make('pilihan_c')
                    ->label('Pilihan C')
                    ->hidden(fn($get) =>  $get('tipe_pertanyaan') === 'benar_salah' || $get('tipe_jawaban') === 'gambar'),
                Forms\Components\FileUpload::make('pilihan_c_gambar')
                    ->label('Pilihan C (Gambar)')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                    ->directory('jawaban/pilihan_c')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $timestamp = Carbon::now()->format('YmdHis');
                        $namaFile = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

                        return "{$timestamp}-pilihan_c-{$namaFile}.{$file->getClientOriginalExtension()}";
                    })
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'text'),
                // Pilihan D
                Forms\Components\TextInput::make('pilihan_d')
                    ->label('Pilihan D')
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'gambar' || $get('tipe_pertanyaan') === 'benar_salah'),
                Forms\Components\FileUpload::make('pilihan_d_gambar')
                    ->label('Pilihan D (Gambar)')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                    ->directory('jawaban/pilihan_d')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $timestamp = Carbon::now()->format('YmdHis');
                        $namaFile = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

                        return "{$timestamp}-pilihan_d-{$namaFile}.{$file->getClientOriginalExtension()}";
                    })
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'text'),
                // Pilihan E
                Forms\Components\TextInput::make('pilihan_e')
                    ->label('Pilihan E')
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'gambar' || $get('tipe_pertanyaan') === 'benar_salah'),
                Forms\Components\FileUpload::make('pilihan_e_gambar')
                    ->label('Pilihan E (Gambar)')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                    ->directory('jawaban/pilihan_e')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $timestamp = Carbon::now()->format('YmdHis');
                        $namaFile = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

                        return "{$timestamp}-pilihan_e-{$namaFile}.{$file->getClientOriginalExtension()}";
                    })
                    ->hidden(fn($get) => $get('tipe_jawaban') === 'text'),
                Forms\Components\CheckboxList::make('jawaban')
                    ->label('Jawaban')
                    ->options(
                        fn($get) => $get('tipe_pertanyaan') === 'benar_salah'
                            ? ['A' => 'Pilihan A', 'B' => 'Pilihan B']
                            : ['A' => 'Pilihan A', 'B' => 'Pilihan B', 'C' => 'Pilihan C', 'D' => 'Pilihan D', 'E' => 'Pilihan E']
                    )
                    ->columns(2)
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(
                        fn($component, $state) =>
                        $component->state(is_string($state) ? explode(',', $state) : $state)
                    )
                    ->dehydrateStateUsing(fn($state) => is_array($state) ? implode(',', $state) : $state)
                    ->minItems(1)
                    ->maxItems(fn($get) => $get('tipe_pertanyaan') === 'opsi_jawaban' ? 5 : 1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->getStateUsing(fn($rowLoop) => $rowLoop->iteration)
                    ->searchable()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('ujian.nama_ujian')
                    ->label('Nama Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jawaban')
                    ->label('Jawaban')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('id_ujian')
                    ->label('Nama Ujian')
                    ->relationship('ujian', 'nama_ujian')
                    ->searchable()
                    ->placeholder('Pilih Ujian')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSoals::route('/'),
            'create' => Pages\CreateSoal::route('/create'),
            // 'edit' => Pages\EditSoal::route('/{record}/edit'),
        ];
    }
}
