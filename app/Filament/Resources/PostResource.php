<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\Actions\CreateAction;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Grid::make('2')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            $set('slug', Str::slug($state));
                        }),
                    \Filament\Forms\Components\Hidden::make('slug'),
                    \Filament\Forms\Components\Select::make('categori_id')
                    ->relationship('categori', 'name')
                    ->required(),
                ]),
                \Filament\Forms\Components\Grid::make('1')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('image')
                    ->disk('public')
                    ->directory('attachments')
                    ->visibility('public')
                    ->required(),
                ]),
                \Filament\Forms\Components\Grid::make('1')
                ->schema([
                    \Filament\Forms\Components\RichEditor::make('body')
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('attachments')
                        ->fileAttachmentsVisibility('public')
                        ->required(),
                ]),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id',auth()->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                \Filament\Tables\Columns\ImageColumn::make('image')->sortable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')->label('Author')->sortable()->searchable(),
                \Filament\Tables\Columns\TextColumn::make('categori.name')->label('Category')->sortable()->searchable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
