CREATE TABLE IF NOT EXISTS public.users (
  id SERIAL PRIMARY KEY,
  name CHARACTER VARYING(255),
  email CHARACTER VARYING(255) NOT NULL,
  created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
  updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
);
