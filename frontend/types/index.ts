export interface User {
  id: number
  name: string
  email: string
  email_verified_at?: string
  created_at: string
  updated_at: string
}

export interface TouristSpot {
  id: number
  name: string
  description: string
  prefecture: string
  city?: string
  address?: string
  latitude?: number
  longitude?: number
  category?: string
  images?: string[]
  imageUrl?: string
  tags?: string[]
  audioUrl?: string
  access_info?: string
  website?: string
  phone?: string
  opening_hours?: string
  admission_fee?: string
  is_active?: boolean
  created_at: string
  updated_at: string
  active_guides?: Guide[]
  overview?: string
  highlights?: string[]
  history?: string
  public_transport?: PublicTransport[]
  car_access?: CarAccess[]
  parking_info?: string
  walking_info?: string
  place_id?: string
}

export interface PublicTransport {
  route: string
  station: string
  time: string
}

export interface CarAccess {
  from: string
  exit: string
  time: string
}

export interface Guide {
  id: number
  tourist_spot_id: number
  title: string
  content: string
  type: string
  duration_minutes?: number
  order: number
  highlights?: string[]
  is_active: boolean
  created_at: string
  updated_at: string
  active_audio_guides?: AudioGuide[]
}

export interface AudioGuide {
  id: number
  title: string
  audio_url: string
  duration: number
  language: string
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterCredentials {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface AuthResponse {
  user: User
  token: string
}