<?php

namespace App\Livewire;

use App\Models\Hasil_ujian;
use Livewire\Component;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Attributes\Url;
use App\Filament\Resources\HasilUjianResource\Pages;
use Filament\Resources\Resource;

class HasilUjianList extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $model = Hasil_ujian::class;

    #[Url]
    public int|null $id = null;

    public function mount($id = null)
    {
        if (!$id) {
            $this->id = request()->route('record');
        }
    }

    public static function formSchema(): array
    {
        return [
            Forms\Components\Select::make('id_ujian')
                ->label('Nama Ujian')
                ->relationship('ujian', 'nama_ujian')
                ->required(),
            Forms\Components\Select::make('id_siswa')
                ->label('Nama Siswa')
                ->relationship('siswa', 'nama_siswa')
                ->required(),
            Forms\Components\TextInput::make('jumlah_soal')
                ->label('Jumlah Soal')
                ->integer()
                ->required(),
            Forms\Components\TextInput::make('jumlah_benar')
                ->label('Jumlah Benar')
                ->integer()
                ->required(),
            Forms\Components\TextInput::make('jumlah_salah')
                ->label('Jumlah Salah')
                ->integer()
                ->required(),
            Forms\Components\TextInput::make('nilai')
                ->label('Nilai')
                ->integer()
                ->required(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hasil_ujian::query()->when($this->id, fn($query) => $query->where('id_ujian', $this->id))
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->getStateUsing(fn($rowLoop) => $rowLoop->iteration)
                    ->sortable(false),
                TextColumn::make('ujian.nama_ujian')
                    ->label('Nama Ujian')
                    ->searchable(),
                TextColumn::make('siswa.nama_siswa')
                    ->label('Nama Siswa')
                    ->searchable(),
                TextColumn::make('jumlah_benar')
                    ->label('Jumlah Benar')
                    ->searchable(),
                TextColumn::make('jumlah_salah')
                    ->label('Jumlah Salah')
                    ->searchable(),
                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->searchable(),
            ])
            ->filters([
                // ...  
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(static::formSchema())
                    ->button()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->modalHeading('Hapus Hasil Ujian')
                    ->modalSubheading('Apakah Anda yakin ingin menghapus Hasil Ujian ini?'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('print')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->url(fn () => route('export.hasil.ujian', ['id' => $this->id]), shouldOpenInNewTab: true)
                    ->color('primary'),
            ])
            ->bulkActions([
                // ...  
            ]);
    }

    public static function getPages(): array
    {
        return [
            // 'index' => Pages\ListHasilUjians::route('/'),
            // 'create' => Pages\CreateHasilUjian::route('/create'),
            // 'edit' => Pages\EditHasilUjian::route('/{record}/edit'),
        ];
    }

    public function render()
    {
        return view('livewire.hasil-ujian-list');
    }
}
